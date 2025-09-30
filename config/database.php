<?php

/**
 * Configurações de banco de dados
 * 
 * Define conexões para MySQL (tarefas) e MongoDB (logs)
 * seguindo o princípio de separação de responsabilidades.
 */

return [
    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [
        // Conexão MySQL para tarefas (banco relacional)
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'task_management'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ],

        // Conexão MongoDB para logs (banco não-relacional)
        'mongodb' => [
            'driver' => 'mongodb',
            'host' => env('MONGODB_HOST', '127.0.0.1'),
            'port' => env('MONGODB_PORT', 27017),
            'database' => env('MONGODB_DATABASE', 'task_logs'),
            'username' => env('MONGODB_USERNAME'),
            'password' => env('MONGODB_PASSWORD'),
            'options' => [
                'authSource' => env('MONGODB_AUTHENTICATION_DATABASE', 'task_logs'),
            ],
        ],
    ],

    'migrations' => 'migrations',
];

