<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Em vez de '*', usamos uma variável de ambiente.
    // No seu arquivo .env, adicione a linha:
    // CORS_ALLOWED_ORIGINS=http://localhost:3000,http://127.0.0.1:8000
    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', '')),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Mude para 'true' se seu frontend precisar enviar cookies de autenticação.
    'supports_credentials' => true,

];
