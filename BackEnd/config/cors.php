<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'https://kampusresiks.gt.tc',
        'https://kampusresiku.rf.gd',
        'http://localhost',
        'http://127.0.0.1',
        'http://localhost:8000',
        'http://localhost:8001',
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];