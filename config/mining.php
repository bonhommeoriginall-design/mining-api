<?php

return [
    'llm' => [
        'api_key' => env('MINING_LLM_API_KEY', ''),
        'base_url' => rtrim(env('MINING_LLM_BASE_URL', 'https://api.openai.com/v1'), '/'),
        'model' => env('MINING_LLM_MODEL', 'gpt-4o-mini'),
        'enabled' => (bool) env('MINING_LLM_ENABLED', true),
    ],
];
