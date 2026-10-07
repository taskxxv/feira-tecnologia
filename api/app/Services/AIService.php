<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AIService
{
    private function provider(): string
    {
        return config('services.ai.provider', 'gemini');
    }

    private function client(): PendingRequest
    {
        $key = config('services.openai.key');

        if (!$key) {
            throw new RuntimeException('Serviço de IA não configurado.');
        }

        return Http::baseUrl(config('services.openai.base_url'))
            ->withToken($key)
            ->timeout((int) env('AI_TIMEOUT', 15));
    }

    private function geminiClient(): PendingRequest
    {
        $key = config('services.gemini.key');

        if (!$key) {
            throw new RuntimeException('Serviço Gemini não configurado.');
        }

        return Http::baseUrl(config('services.gemini.base_url'))
            ->withHeader('x-goog-api-key', $key)
            ->acceptJson()
            ->timeout((int) env('AI_TIMEOUT', 15));
    }

    private function geminiGenerate(string $system, string $prompt, bool $json = false): string
    {
        $generationConfig = $json
            ? ['responseMimeType' => 'application/json']
            : [];

        return $this->geminiClient()
            ->post('/models/' . config('services.gemini.chat_model') . ':generateContent', [
                'systemInstruction' => ['parts' => [['text' => $system]]],
                'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                'generationConfig' => $generationConfig,
            ])
            ->throw()
            ->json('candidates.0.content.parts.0.text') ?? '';
    }

    public function moderate(string $text): array
    {
        try {
            if ($this->provider() === 'gemini') {
                $analysis = json_decode($this->geminiGenerate(
                    'Classifique conteúdo de fórum escolar. Retorne JSON com allowed (boolean) e reason (string). Bloqueie ameaças, discurso de ódio, sexualização de menores e dados pessoais.',
                    $text,
                    true
                ), true);

                return [
                    'allowed' => (bool) ($analysis['allowed'] ?? false),
                    'reason' => $analysis['reason'] ?? 'Não aprovado pela moderação.',
                ];
            }

            $content = $this->client()
                ->post('/chat/completions', [
                    'model' => env('OPENAI_CHAT_MODEL', 'gpt-4o-mini'),
                    'temperature' => 0,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Classifique conteúdo de fórum escolar. Retorne JSON {"allowed":boolean,"reason":string}. Bloqueie ameaças, discurso de ódio, sexualização de menores e dados pessoais.',
                        ],
                        ['role' => 'user', 'content' => $text],
                    ],
                ])
                ->throw()
                ->json('choices.0.message.content');

            $analysis = json_decode($content ?: '{}', true);

            return [
                'allowed' => (bool) ($analysis['allowed'] ?? false),
                'reason' => $analysis['reason'] ?? 'Não aprovado pela moderação.',
            ];
        } catch (\Throwable $exception) {
            throw new RuntimeException('Falha na moderação de conteúdo.', 0, $exception);
        }
    }

    public function embedding(string $text): array
    {
        try {
            if ($this->provider() === 'gemini') {
                return $this->geminiClient()
                    ->post('/models/' . config('services.gemini.embedding_model') . ':embedContent', [
                        'model' => 'models/' . config('services.gemini.embedding_model'),
                        'content' => ['parts' => [['text' => $text]]],
                        'outputDimensionality' => (int) config('services.gemini.embedding_dimensions', 1536),
                    ])
                    ->throw()
                    ->json('embedding.values') ?? [];
            }

            return $this->client()
                ->post('/embeddings', [
                    'model' => env('OPENAI_MODEL', 'text-embedding-3-small'),
                    'input' => $text,
                ])
                ->throw()
                ->json('data.0.embedding') ?? [];
        } catch (\Throwable $exception) {
            throw new RuntimeException('Falha ao gerar embedding.', 0, $exception);
        }
    }

    public function answer(string $question, array $context = []): string
    {
        if ($context === []) {
            return $this->pedagogicalFallback($question);
        }

        try {
            if ($this->provider() === 'gemini') {
                return $this->geminiGenerate(
                    'Você é um tutor escolar. Responda em português usando exclusivamente o contexto recuperado. Cite as fontes pelo formato [Autor: título]. Se o contexto não responder, diga claramente que não encontrou material suficiente.',
                    "Contexto recuperado:\n"
                        . implode("\n\n", $context)
                        . "\n\nPergunta: "
                        . $question
                ) ?: 'Não foi possível responder.';
            }

            return $this->client()
                ->post('/chat/completions', [
                    'model' => env('OPENAI_CHAT_MODEL', 'gpt-4o-mini'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Você é um tutor escolar. Responda em português usando exclusivamente o contexto recuperado. Cite as fontes pelo formato [Autor: título]. Se o contexto não responder, diga claramente que não encontrou material suficiente.',
                        ],
                        [
                            'role' => 'user',
                            'content' => "Contexto recuperado:\n"
                                . implode("\n\n", $context)
                                . "\n\nPergunta: "
                                . $question,
                        ],
                    ],
                ])
                ->throw()
                ->json('choices.0.message.content')
                ?? 'Não foi possível responder.';
        } catch (\Throwable $exception) {
            throw new RuntimeException('Falha no tutor de IA.', 0, $exception);
        }
    }

    private function pedagogicalFallback(string $question): string
    {
        $topic = rawurlencode($question);

        return "Não encontrei material da escola relacionado a essa pergunta. "
            . "Revise o conceito, procure um exemplo resolvido e tente explicar "
            . "o conteúdo com suas próprias palavras.\n\n"
            . "Sugestões de estudo no YouTube:\n"
            . "- Khan Academy Brasil: https://www.youtube.com/results?search_query=Khan+Academy+Brasil+{$topic}\n"
            . "- Me Salva!: https://www.youtube.com/results?search_query=Me+Salva+{$topic}\n"
            . "- Brasil Escola: https://www.youtube.com/results?search_query=Brasil+Escola+{$topic}";
    }

    public function filterReport(string $content): array
    {
        $url = config('services.ombudsman.url');

        if ($url) {
            try {
                return Http::withToken(config('services.ombudsman.token'))
                    ->timeout((int) env('AI_TIMEOUT', 15))
                    ->post($url, ['content' => $content])
                    ->throw()
                    ->json();
            } catch (\Throwable $exception) {
                throw new RuntimeException('Falha no filtro de ouvidoria.', 0, $exception);
            }
        }

        try {
            if ($this->provider() === 'gemini') {
                $analysis = json_decode($this->geminiGenerate(
                    'Você é o filtro de uma ouvidoria escolar. Retorne somente JSON com allowed (boolean), category (infraestrutura, pedagogico, bullying, administrativo ou geral), severity (baixa, media ou alta) e summary. Bloqueie spam, palavrões gratuitos, ameaças e conteúdo sem relação com a escola.',
                    $content,
                    true
                ), true, 512, JSON_THROW_ON_ERROR);

                return [
                    'allowed' => (bool) ($analysis['allowed'] ?? false),
                    'category' => in_array(
                        $analysis['category'] ?? null,
                        ['infraestrutura', 'pedagogico', 'bullying', 'administrativo', 'geral'],
                        true
                    ) ? $analysis['category'] : 'geral',
                    'severity' => in_array(
                        $analysis['severity'] ?? null,
                        ['baixa', 'media', 'alta'],
                        true
                    ) ? $analysis['severity'] : 'media',
                    'summary' => is_string($analysis['summary'] ?? null)
                        ? $analysis['summary']
                        : null,
                ];
            }

            $raw = $this->client()
                ->post('/chat/completions', [
                    'model' => env('OPENAI_CHAT_MODEL', 'gpt-4o-mini'),
                    'temperature' => 0,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Você é o filtro de uma ouvidoria escolar. Retorne somente JSON com allowed (boolean), category (infraestrutura, pedagogico, bullying, administrativo ou geral), severity (baixa, media ou alta) e summary. Bloqueie spam, palavrões gratuitos, ameaças e conteúdo sem relação com a escola.',
                        ],
                        ['role' => 'user', 'content' => $content],
                    ],
                ])
                ->throw()
                ->json('choices.0.message.content');

            $analysis = json_decode($raw ?: '{}', true, 512, JSON_THROW_ON_ERROR);

            return [
                'allowed' => (bool) ($analysis['allowed'] ?? false),
                'category' => in_array(
                    $analysis['category'] ?? null,
                    ['infraestrutura', 'pedagogico', 'bullying', 'administrativo', 'geral'],
                    true
                ) ? $analysis['category'] : 'geral',
                'severity' => in_array(
                    $analysis['severity'] ?? null,
                    ['baixa', 'media', 'alta'],
                    true
                ) ? $analysis['severity'] : 'media',
                'summary' => is_string($analysis['summary'] ?? null)
                    ? $analysis['summary']
                    : null,
            ];
        } catch (\Throwable $exception) {
            throw new RuntimeException('Falha no filtro de ouvidoria.', 0, $exception);
        }
    }
}
