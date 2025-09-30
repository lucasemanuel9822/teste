<?php

/**
 * Configurações de cache
 * 
 * Utiliza Redis para cache e filas, implementando
 * o padrão cache-aside para melhor performance.
 */

return [
    'default' => env('CACHE_DRIVER', 'redis'),

    'stores' => [
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],
    ],

    'prefix' => env('CACHE_PREFIX', 'task_management'),
];

