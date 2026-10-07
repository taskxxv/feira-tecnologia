<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Requests\PostRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostEmbedding;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(Request $request)
    {
        return Post::with(['user:id,name', 'subject:id,name'])->withCount('comments')
            ->where('status', Post::STATUS_APPROVED)
            ->when(
                $request->subject_id,
                fn ($query, $subjectId) => $query->where('subject_id', $subjectId)
            )
            ->latest()
            ->paginate(15);
    }

    public function mine(Request $request)
    {
        return $request->user()->posts()
            ->with(['user:id,name', 'subject:id,name'])->withCount('comments')
            ->where('status', '!=', Post::STATUS_APPROVED)
            ->latest()->paginate(15);
    }

    public function store(PostRequest $request, AIService $ai)
    {
        $data = $request->validated();
        // Preserve o texto antes de chamar o serviço externo. Um post pendente
        // só pode ser lido pelo próprio autor e pela administração.
        $post = Post::create([
            ...$data,
            'user_id' => $request->user()->id,
            'status' => Post::STATUS_PENDING,
        ]);
        $notice = null;
        try {
            $moderation = $ai->moderate($data['title'] . "\n" . $data['content']);
            if ($moderation['allowed']) {
                $embedding = $ai->embedding($post->content);
                if ($embedding === []) {
                    throw new \RuntimeException('Embedding vazio.');
                }
                DB::transaction(function () use ($post, $embedding): void {
                    PostEmbedding::create([
                        'post_id' => $post->id,
                        'embedding' => '[' . implode(',', $embedding) . ']',
                    ]);
                    $post->update(['status' => Post::STATUS_APPROVED]);
                });
            } else {
                $post->update(['status' => Post::STATUS_REJECTED]);
                $notice = 'Publicação salva, mas não aprovada pela moderação.';
            }
        } catch (\Throwable $exception) {
            report($exception);
            $post->refresh();
            $notice = 'Texto salvo como pendente. A análise por IA não pôde ser concluída. Ele ainda não aparece no feed público.';
        }
        $post->load(['user:id,name', 'subject'])->loadCount('comments');
        return response()->json([...$post->toArray(), 'notice' => $notice], 201);
    }

    public function show(Post $post)
    {
        $viewer = Auth::guard('sanctum')->user();
        abort_unless(
            $post->status === Post::STATUS_APPROVED
                || $viewer?->isAdmin()
                || ($viewer && (int) $viewer->id === (int) $post->user_id),
            404
        );

        return $post->load([
            'user:id,name',
            'subject',
            'comments.user:id,name',
        ]);
    }

    public function comment(CommentRequest $request, Post $post)
    {
        abort_unless($post->status === Post::STATUS_APPROVED, 404);

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $request->content,
        ]);

        return response()->json($comment->load('user:id,name'), 201);
    }

    public function moderate(Request $request, Post $post)
    {
        $request->validate([
            'status' => 'required|in:aprovado,pendente,reprovado',
        ]);

        $post->update(['status' => $request->status]);

        return $post;
    }
}
