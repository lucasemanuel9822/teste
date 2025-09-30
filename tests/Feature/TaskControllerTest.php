<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TaskControllerTest
 * 
 * Testes de integração para o TaskController.
 * Testa todos os endpoints relacionados a tarefas.
 */
class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * API Key para testes
     * 
     * @var string
     */
    protected string $apiKey = 'test-api-key-123';

    /**
     * Configuração inicial dos testes
     * 
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Define a API Key para testes
        putenv("API_KEY={$this->apiKey}");
    }

    /**
     * Testa listagem de tarefas
     * 
     * @return void
     */
    public function test_can_list_tasks(): void
    {
        // Cria tarefas de teste
        Task::factory()->create(['status' => 'pending']);
        Task::factory()->create(['status' => 'in_progress']);
        Task::factory()->create(['status' => 'completed']);

        // Faz requisição
        $response = $this->get('/tasks');

        // Verifica resposta
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                    'status',
                    'status_label',
                    'created_at',
                    'updated_at',
                    'is_pending',
                    'is_in_progress',
                    'is_completed'
                ]
            ]
        ]);
    }

    /**
     * Testa listagem de tarefas com filtro por status
     * 
     * @return void
     */
    public function test_can_list_tasks_with_status_filter(): void
    {
        // Cria tarefas de teste
        Task::factory()->create(['status' => 'pending']);
        Task::factory()->create(['status' => 'in_progress']);
        Task::factory()->create(['status' => 'completed']);

        // Faz requisição com filtro
        $response = $this->get('/tasks?status=pending');

        // Verifica resposta
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.status', 'pending');
    }

    /**
     * Testa criação de tarefa
     * 
     * @return void
     */
    public function test_can_create_task(): void
    {
        $taskData = [
            'title' => 'Tarefa de Teste',
            'description' => 'Descrição da tarefa de teste',
            'status' => 'pending'
        ];

        // Faz requisição
        $response = $this->post('/tasks', $taskData, [
            'X-API-KEY' => $this->apiKey
        ]);

        // Verifica resposta
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'title',
                'description',
                'status',
                'status_label',
                'created_at',
                'updated_at'
            ]
        ]);
        $response->assertJsonPath('data.title', 'Tarefa de Teste');
        $response->assertJsonPath('data.status', 'pending');
    }

    /**
     * Testa criação de tarefa sem API Key
     * 
     * @return void
     */
    public function test_cannot_create_task_without_api_key(): void
    {
        $taskData = [
            'title' => 'Tarefa de Teste',
            'description' => 'Descrição da tarefa de teste',
            'status' => 'pending'
        ];

        // Faz requisição sem API Key
        $response = $this->post('/tasks', $taskData);

        // Verifica resposta
        $response->assertStatus(401);
        $response->assertJsonStructure([
            'error',
            'message',
            'code'
        ]);
    }

    /**
     * Testa criação de tarefa com dados inválidos
     * 
     * @return void
     */
    public function test_cannot_create_task_with_invalid_data(): void
    {
        $taskData = [
            'title' => '', // Título vazio
            'status' => 'invalid_status' // Status inválido
        ];

        // Faz requisição
        $response = $this->post('/tasks', $taskData, [
            'X-API-KEY' => $this->apiKey
        ]);

        // Verifica resposta
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'error',
            'message',
            'details'
        ]);
    }

    /**
     * Testa busca de tarefa por ID
     * 
     * @return void
     */
    public function test_can_get_task_by_id(): void
    {
        // Cria tarefa de teste
        $task = Task::factory()->create();

        // Faz requisição
        $response = $this->get("/tasks/{$task->id}");

        // Verifica resposta
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'description',
                'status',
                'status_label',
                'created_at',
                'updated_at'
            ]
        ]);
        $response->assertJsonPath('data.id', $task->id);
    }

    /**
     * Testa busca de tarefa inexistente
     * 
     * @return void
     */
    public function test_cannot_get_nonexistent_task(): void
    {
        // Faz requisição para ID inexistente
        $response = $this->get('/tasks/999');

        // Verifica resposta
        $response->assertStatus(404);
        $response->assertJsonStructure([
            'error',
            'message'
        ]);
    }

    /**
     * Testa atualização de tarefa
     * 
     * @return void
     */
    public function test_can_update_task(): void
    {
        // Cria tarefa de teste
        $task = Task::factory()->create(['status' => 'pending']);

        $updateData = [
            'title' => 'Tarefa Atualizada',
            'status' => 'in_progress'
        ];

        // Faz requisição
        $response = $this->put("/tasks/{$task->id}", $updateData, [
            'X-API-KEY' => $this->apiKey
        ]);

        // Verifica resposta
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'title',
                'description',
                'status',
                'status_label',
                'created_at',
                'updated_at'
            ]
        ]);
        $response->assertJsonPath('data.title', 'Tarefa Atualizada');
        $response->assertJsonPath('data.status', 'in_progress');
    }

    /**
     * Testa atualização de tarefa sem API Key
     * 
     * @return void
     */
    public function test_cannot_update_task_without_api_key(): void
    {
        // Cria tarefa de teste
        $task = Task::factory()->create();

        $updateData = [
            'title' => 'Tarefa Atualizada'
        ];

        // Faz requisição sem API Key
        $response = $this->put("/tasks/{$task->id}", $updateData);

        // Verifica resposta
        $response->assertStatus(401);
        $response->assertJsonStructure([
            'error',
            'message',
            'code'
        ]);
    }

    /**
     * Testa exclusão de tarefa
     * 
     * @return void
     */
    public function test_can_delete_task(): void
    {
        // Cria tarefa de teste
        $task = Task::factory()->create();

        // Faz requisição
        $response = $this->delete("/tasks/{$task->id}", [], [
            'X-API-KEY' => $this->apiKey
        ]);

        // Verifica resposta
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message'
        ]);

        // Verifica se a tarefa foi excluída
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /**
     * Testa exclusão de tarefa sem API Key
     * 
     * @return void
     */
    public function test_cannot_delete_task_without_api_key(): void
    {
        // Cria tarefa de teste
        $task = Task::factory()->create();

        // Faz requisição sem API Key
        $response = $this->delete("/tasks/{$task->id}");

        // Verifica resposta
        $response->assertStatus(401);
        $response->assertJsonStructure([
            'error',
            'message',
            'code'
        ]);
    }

    /**
     * Testa exclusão de tarefa inexistente
     * 
     * @return void
     */
    public function test_cannot_delete_nonexistent_task(): void
    {
        // Faz requisição para ID inexistente
        $response = $this->delete('/tasks/999', [], [
            'X-API-KEY' => $this->apiKey
        ]);

        // Verifica resposta
        $response->assertStatus(404);
        $response->assertJsonStructure([
            'error',
            'message'
        ]);
    }
}



