<?php
return [
    'ai' => [
        'provider' => env('AI_PROVIDER', 'gemini'),
    ],
    'openai' => ['key' => env('OPENAI_API_KEY'), 'base_url' => env('AI_BASE_URL', 'https://api.openai.com/v1')],
    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        'chat_model' => env('GEMINI_CHAT_MODEL', 'gemini-2.5-flash'),
        'embedding_model' => env('GEMINI_EMBEDDING_MODEL', 'gemini-embedding-001'),
        'embedding_dimensions' => env('GEMINI_EMBEDDING_DIMENSIONS', 1536),
    ],
    'ombudsman' => ['url' => env('OMBUDSMAN_FILTER_URL'), 'token' => env('OMBUDSMAN_FILTER_TOKEN')],
];
