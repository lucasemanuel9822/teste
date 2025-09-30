<?php

/**
 * Rotas da aplicação
 *
 * Define todas as rotas da API RESTful conforme especificação do projeto.
 *
 * Endpoints implementados:
 * - POST /tasks: Criar nova tarefa
 * - GET /tasks: Listar tarefas (com filtros)
 * - GET /tasks/{id}: Buscar tarefa por ID
 * - PUT /tasks/{id}: Atualizar tarefa
 * - DELETE /tasks/{id}: Excluir tarefa
 * - GET /logs: Listar logs recentes
 * - GET /logs?id={id}: Buscar log específico
 */

use App\Http\Controllers\LogController;
use App\Http\Controllers\TaskController;

// Rotas de tarefas com rate limiting
$router->group(['prefix' => 'tasks', 'middleware' => 'throttle'], function () use ($router) {
    // GET /tasks - Listar todas as tarefas
    $router->get('/', 'TaskController@index');

    // POST /tasks - Criar nova tarefa
    $router->post('/', 'TaskController@store');

    // GET /tasks/{id} - Buscar tarefa por ID
    $router->get('/{id}', 'TaskController@show');

    // PUT /tasks/{id} - Atualizar tarefa
    $router->put('/{id}', 'TaskController@update');

    // DELETE /tasks/{id} - Excluir tarefa
    $router->delete('/{id}', 'TaskController@destroy');
});

// Rotas de logs com rate limiting
$router->group(['prefix' => 'logs', 'middleware' => 'throttle'], function () use ($router) {
    // GET /logs - Listar logs recentes ou buscar log específico
    $router->get('/', 'LogController@index');
});

// Rota de health check
$router->get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API funcionando corretamente',
        'timestamp' => date('c'), // ISO 8601 format
        'version' => '1.0'
    ]);
});

// Rota de informações da API
$router->get('/info', function () {
    return response()->json([
        'name' => 'Task Management System API',
        'version' => '1.0',
        'description' => 'Sistema de Gerenciamento de Tarefas com Laravel Lumen',
        'endpoints' => [
            'tasks' => [
                'GET /tasks' => 'Listar tarefas',
                'POST /tasks' => 'Criar tarefa',
                'GET /tasks/{id}' => 'Buscar tarefa',
                'PUT /tasks/{id}' => 'Atualizar tarefa',
                'DELETE /tasks/{id}' => 'Excluir tarefa'
            ],
            'logs' => [
                'GET /logs' => 'Listar logs recentes',
                'GET /logs?id={id}' => 'Buscar log específico'
            ]
        ],
        'authentication' => [
            'type' => 'API Key',
            'header' => 'X-API-KEY',
            'required_for' => ['POST', 'PUT', 'DELETE']
        ]
    ]);
});

// Rota para documentação Swagger
$router->get('/swagger.json', 'SwaggerController@index');
