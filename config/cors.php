<?php
return [
    // 'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
    ],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:3001',
        'http://127.0.0.1:3000',
        'https://furbabiessafety.com',
        'https://www.furbabiessafety.com',
        'https://app.furbabiessafety.com',
        'http://furbabiessafety.local',
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 3600,
    'supports_credentials' => true,
];