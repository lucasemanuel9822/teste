<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

/**
 * SwaggerController
 * 
 * Controller para documentação da API usando Swagger/OpenAPI.
 * Gera documentação automática dos endpoints da API.
 */
class SwaggerController extends Controller
{
    /**
     * Retorna a documentação Swagger em JSON
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $swagger = [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Task Management System API',
                'description' => 'Sistema de Gerenciamento de Tarefas com Laravel Lumen',
                'version' => '1.0.0'
            ],
            'servers' => [
                [
                    'url' => env('APP_URL', 'http://localhost:8000'),
                    'description' => 'Servidor de Desenvolvimento'
                ]
            ],
            'paths' => $this->getPaths(),
            'components' => [
                'schemas' => $this->getSchemas(),
                'securitySchemes' => $this->getSecuritySchemes()
            ],
            'tags' => [
                [
                    'name' => 'Tasks',
                    'description' => 'Operações relacionadas a tarefas'
                ],
                [
                    'name' => 'Logs',
                    'description' => 'Operações relacionadas a logs de eventos'
                ]
            ]
        ];

        return response()->json($swagger, 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Retorna os paths da API
     * 
     * @return array
     */
    protected function getPaths(): array
    {
        return [
            '/tasks' => [
                'get' => [
                    'tags' => ['Tasks'],
                    'summary' => 'Lista todas as tarefas',
                    'description' => 'Retorna uma lista de tarefas com suporte a filtros por status',
                    'parameters' => [
                        [
                            'name' => 'status',
                            'in' => 'query',
                            'description' => 'Filtrar por status da tarefa',
                            'required' => false,
                            'schema' => [
                                'type' => 'string',
                                'enum' => ['pending', 'in_progress', 'completed']
                            ]
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Lista de tarefas retornada com sucesso',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'data' => [
                                                'type' => 'array',
                                                'items' => ['$ref' => '#/components/schemas/Task']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'post' => [
                    'tags' => ['Tasks'],
                    'summary' => 'Cria uma nova tarefa',
                    'description' => 'Cria uma nova tarefa no sistema',
                    'security' => [['apiKey' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['title', 'status'],
                                    'properties' => [
                                        'title' => [
                                            'type' => 'string',
                                            'example' => 'Implementar autenticação'
                                        ],
                                        'description' => [
                                            'type' => 'string',
                                            'example' => 'Criar sistema de autenticação usando API Key'
                                        ],
                                        'status' => [
                                            'type' => 'string',
                                            'enum' => ['pending', 'in_progress', 'completed'],
                                            'example' => 'pending'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '201' => [
                            'description' => 'Tarefa criada com sucesso',
                            'content' => [
                                'application/json' => [
                                    'schema' => ['$ref' => '#/components/schemas/Task']
                                ]
                            ]
                        ],
                        '401' => [
                            'description' => 'Não autorizado - API Key inválida'
                        ],
                        '422' => [
                            'description' => 'Dados de validação inválidos'
                        ]
                    ]
                ]
            ],
            '/tasks/{id}' => [
                'get' => [
                    'tags' => ['Tasks'],
                    'summary' => 'Busca tarefa por ID',
                    'description' => 'Retorna os dados de uma tarefa específica',
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'description' => 'ID da tarefa',
                            'required' => true,
                            'schema' => ['type' => 'integer']
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Tarefa encontrada com sucesso',
                            'content' => [
                                'application/json' => [
                                    'schema' => ['$ref' => '#/components/schemas/Task']
                                ]
                            ]
                        ],
                        '404' => [
                            'description' => 'Tarefa não encontrada'
                        ]
                    ]
                ],
                'put' => [
                    'tags' => ['Tasks'],
                    'summary' => 'Atualiza uma tarefa',
                    'description' => 'Atualiza os dados de uma tarefa existente',
                    'security' => [['apiKey' => []]],
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'description' => 'ID da tarefa',
                            'required' => true,
                            'schema' => ['type' => 'integer']
                        ]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'title' => [
                                            'type' => 'string',
                                            'example' => 'Implementar autenticação'
                                        ],
                                        'description' => [
                                            'type' => 'string',
                                            'example' => 'Criar sistema de autenticação usando API Key'
                                        ],
                                        'status' => [
                                            'type' => 'string',
                                            'enum' => ['pending', 'in_progress', 'completed'],
                                            'example' => 'in_progress'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Tarefa atualizada com sucesso',
                            'content' => [
                                'application/json' => [
                                    'schema' => ['$ref' => '#/components/schemas/Task']
                                ]
                            ]
                        ],
                        '401' => [
                            'description' => 'Não autorizado - API Key inválida'
                        ],
                        '404' => [
                            'description' => 'Tarefa não encontrada'
                        ],
                        '422' => [
                            'description' => 'Dados de validação inválidos'
                        ]
                    ]
                ],
                'delete' => [
                    'tags' => ['Tasks'],
                    'summary' => 'Exclui uma tarefa',
                    'description' => 'Remove uma tarefa do sistema',
                    'security' => [['apiKey' => []]],
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'description' => 'ID da tarefa',
                            'required' => true,
                            'schema' => ['type' => 'integer']
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Tarefa excluída com sucesso'
                        ],
                        '401' => [
                            'description' => 'Não autorizado - API Key inválida'
                        ],
                        '404' => [
                            'description' => 'Tarefa não encontrada'
                        ]
                    ]
                ]
            ],
            '/logs' => [
                'get' => [
                    'tags' => ['Logs'],
                    'summary' => 'Lista logs recentes',
                    'description' => 'Retorna os últimos 30 logs da aplicação ou um log específico por ID',
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'query',
                            'description' => 'ID do log específico (opcional)',
                            'required' => false,
                            'schema' => ['type' => 'string']
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Logs retornados com sucesso',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'data' => [
                                                'type' => 'array',
                                                'items' => ['$ref' => '#/components/schemas/Log']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        '404' => [
                            'description' => 'Log não encontrado'
                        ]
                    ]
                ]
            ],
            '/health' => [
                'get' => [
                    'tags' => ['System'],
                    'summary' => 'Health Check',
                    'description' => 'Verifica se a API está funcionando corretamente',
                    'responses' => [
                        '200' => [
                            'description' => 'API funcionando corretamente',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'status' => ['type' => 'string', 'example' => 'ok'],
                                            'message' => ['type' => 'string', 'example' => 'API funcionando corretamente'],
                                            'timestamp' => ['type' => 'string', 'format' => 'date-time'],
                                            'version' => ['type' => 'string', 'example' => '1.0']
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Retorna os schemas da API
     * 
     * @return array
     */
    protected function getSchemas(): array
    {
        return [
            'Task' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer', 'example' => 1],
                    'title' => ['type' => 'string', 'example' => 'Implementar autenticação'],
                    'description' => ['type' => 'string', 'example' => 'Criar sistema de autenticação usando API Key'],
                    'status' => ['type' => 'string', 'enum' => ['pending', 'in_progress', 'completed'], 'example' => 'pending'],
                    'created_at' => ['type' => 'string', 'format' => 'date-time'],
                    'updated_at' => ['type' => 'string', 'format' => 'date-time']
                ]
            ],
            'Log' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'string', 'example' => '507f1f77bcf86cd799439011'],
                    'entity_type' => ['type' => 'string', 'example' => 'task'],
                    'entity_id' => ['type' => 'string', 'example' => '1'],
                    'action' => ['type' => 'string', 'enum' => ['created', 'updated', 'deleted'], 'example' => 'created'],
                    'action_label' => ['type' => 'string', 'example' => 'Criado'],
                    'data' => ['type' => 'object'],
                    'created_at' => ['type' => 'string', 'format' => 'date-time'],
                    'updated_at' => ['type' => 'string', 'format' => 'date-time']
                ]
            ],
            'Error' => [
                'type' => 'object',
                'properties' => [
                    'error' => ['type' => 'string', 'example' => 'Erro de validação'],
                    'message' => ['type' => 'string', 'example' => 'Os dados fornecidos não atendem aos requisitos'],
                    'details' => ['type' => 'object']
                ]
            ]
        ];
    }

    /**
     * Retorna os esquemas de segurança
     * 
     * @return array
     */
    protected function getSecuritySchemes(): array
    {
        return [
            'apiKey' => [
                'type' => 'apiKey',
                'in' => 'header',
                'name' => 'X-API-KEY',
                'description' => 'Chave de API para autenticação'
            ]
        ];
    }
}


