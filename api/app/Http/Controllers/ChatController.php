<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatRequest;
use App\Models\Post;
use App\Models\PostEmbedding;
use App\Services\AIService;

class ChatController extends Controller
{
    public function __invoke(ChatRequest $request, AIService $ai)
    {
        try {
            $question = $request->string('message')->toString();
            $vector = $ai->embedding($question);

            if ($vector === []) {
                throw new \RuntimeException('Embedding da pergunta vazio.');
            }

            // O pgvector ordena pelo menor valor de distância de cosseno.
            // Somente posts aprovados podem fornecer contexto ao modelo.
            $vectorLiteral = '[' . implode(',', array_map(
                static fn ($value): string => (string) $value,
                $vector
            )) . ']';

            $matches = PostEmbedding::query()
                ->with(['post.user:id,name', 'post.subject:id,name'])
                ->whereHas('post', function ($query) use ($request): void {
                    $query
                        ->where('status', Post::STATUS_APPROVED)
                        ->when(
                            $request->subject_id,
                            fn ($query, $subjectId) => $query->where('subject_id', $subjectId)
                        );
                })
                ->select(['vetores_publicacoes.*'])
                ->selectRaw(
                    'vetores_publicacoes.embedding <=> ?::vector AS distance',
                    [$vectorLiteral]
                )
                ->orderBy('distance')
                ->limit(5)
                ->get();

            $context = $matches
                ->map(function (PostEmbedding $embedding): string {
                    $post = $embedding->post;
                    $author = $post->user?->name ?? 'Autor não identificado';
                    $subject = $post->subject?->name
                        ? " ({$post->subject->name})"
                        : '';

                    return "[Autor: {$author}; título: {$post->title}{$subject}]\n"
                        . $post->content;
                })
                ->values()
                ->all();

            return [
                'answer' => $ai->answer($question, $context),
                'rag' => [
                    'used' => $context !== [],
                    'sources' => $matches
                        ->map(fn (PostEmbedding $embedding): array => [
                            'post_id' => $embedding->post->id,
                            'title' => $embedding->post->title,
                            'author' => $embedding->post->user?->name,
                            'subject' => $embedding->post->subject?->name,
                            'distance' => (float) $embedding->distance,
                        ])
                        ->values(),
                ],
            ];
        } catch (\RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 503);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Não foi possível consultar a base de conhecimento.',
            ], 503);
        }
    }
}
