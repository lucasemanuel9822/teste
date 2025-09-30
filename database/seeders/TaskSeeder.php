<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

/**
 * Seeder para popular a tabela de tarefas
 * 
 * Cria dados de exemplo para desenvolvimento e testes.
 */
class TaskSeeder extends Seeder
{
    /**
     * Executa o seeder
     * 
     * @return void
     */
    public function run(): void
    {
        $tasks = [
            [
                'title' => 'Implementar autenticação API',
                'description' => 'Criar sistema de autenticação usando API Key para proteger os endpoints',
                'status' => Task::STATUS_COMPLETED,
            ],
            [
                'title' => 'Configurar banco de dados',
                'description' => 'Configurar conexões MySQL e MongoDB com Docker',
                'status' => Task::STATUS_IN_PROGRESS,
            ],
            [
                'title' => 'Implementar endpoints RESTful',
                'description' => 'Criar todos os endpoints para CRUD de tarefas',
                'status' => Task::STATUS_PENDING,
            ],
            [
                'title' => 'Configurar sistema de logs',
                'description' => 'Implementar logging assíncrono no MongoDB',
                'status' => Task::STATUS_PENDING,
            ],
            [
                'title' => 'Criar documentação Swagger',
                'description' => 'Documentar todos os endpoints da API',
                'status' => Task::STATUS_PENDING,
            ],
            [
                'title' => 'Implementar testes unitários',
                'description' => 'Criar testes para todos os endpoints e funcionalidades',
                'status' => Task::STATUS_PENDING,
            ],
            [
                'title' => 'Configurar Docker Compose',
                'description' => 'Criar ambiente de desenvolvimento com containers',
                'status' => Task::STATUS_IN_PROGRESS,
            ],
            [
                'title' => 'Implementar cache Redis',
                'description' => 'Configurar cache para melhorar performance da API',
                'status' => Task::STATUS_PENDING,
            ],
        ];

        foreach ($tasks as $taskData) {
            Task::create($taskData);
        }
    }
}

