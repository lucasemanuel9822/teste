<?php

/**
 * Configurações da aplicação
 * 
 * Define configurações globais como nome, ambiente,
 * chave de criptografia e timezone.
 */

return [
    'name' => env('APP_NAME', 'TaskManagementSystem'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'locale' => 'pt_BR',
    'fallback_locale' => 'en',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'log' => env('LOG_CHANNEL', 'stack'),
    'log_level' => env('LOG_LEVEL', 'debug'),
];

