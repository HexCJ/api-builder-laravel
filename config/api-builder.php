<?php

return [
    'table' => 'api_endpoints',
    'route_prefix' => 'api',
    'builder_prefix' => 'api-builder',
    'builder_middleware' => ['web', 'auth'],
    'api_middleware' => ['api'],
    'auth' => [
        'sanctum' => 'auth:sanctum',
        'passport' => 'auth:api',
        'custom' => env('API_BUILDER_AUTH_MIDDLEWARE'),
    ],
    'metadata' => [
        'blocked_tables' => ['migrations', 'password_reset_tokens', 'sessions'],
    ],
    'security' => [
        'allow_raw_expressions' => false,
        'max_limit' => 500,
        'default_limit' => 50,
    ],
];
