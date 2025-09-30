<?php

namespace Tests\Unit;

use App\Contracts\Repositories\TaskRepositoryInterface;
use App\Contracts\Services\LogServiceInterface;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

/**
 * TaskServiceTest
 * 
 * Testes unitários para o TaskService.
 * Testa a lógica de negócio relacionada a tarefas.
 */
class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Repositório mock de tarefas
     * 
     * @var Mockery\MockInterface
     */
    protected $taskRepositoryMock;

    /**
     * Serviço mock de logs
     * 
     * @var Mockery\MockInterface
     */
    protected $logServiceMock;

    /**
     * Serviço de tarefas
     * 
     * @var TaskService
     */
    protected TaskService $taskService;

    /**
     * Configuração inicial dos testes
     * 
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Cria mocks
        $this->taskRepositoryMock = Mockery::mock(TaskRepositoryInterface::class);
        $this->logServiceMock = Mockery::mock(LogServiceInterface::class);

        // Cria instância do serviço
        $this->taskService = new TaskService(
            $this->taskRepositoryMock,
            $this->logServiceMock
        );
    }

    /**
     * Limpa mocks após cada teste
     * 
     * @return void
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Testa criação de tarefa com dados válidos
     * 
     * @return void
     */
    public function test_can_create_task_with_valid_data(): void
    {
        // Dados de entrada
        $data = [
            'title' => 'Tarefa de Teste',
            'description' => 'Descrição da tarefa',
            'status' => 'pending'
        ];

        // Tarefa criada
        $task = new Task($data);
        $task->id = 1;

        // Configura mocks
        $this->taskRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($task);

        $this->logServiceMock
            ->shouldReceive('logTaskCreated')
            ->once()
            ->with($task);

        // Executa teste
        $result = $this->taskService->createTask($data);

        // Verifica resultado
        $this->assertInstanceOf(Task::class, $result);
        $this->assertEquals('Tarefa de Teste', $result->title);
        $this->assertEquals('pending', $result->status);
    }

    /**
     * Testa criação de tarefa com dados inválidos
     * 
     * @return void
     */
    public function test_cannot_create_task_with_invalid_data(): void
    {
        // Dados inválidos
        $data = [
            'title' => '', // Título vazio
            'status' => 'invalid_status' // Status inválido
        ];

        // Executa teste e verifica exceção
        $this->expectException(ValidationException::class);
        $this->taskService->createTask($data);
    }

    /**
     * Testa atualização de tarefa existente
     * 
     * @return void
     */
    public function test_can_update_existing_task(): void
    {
        // Tarefa existente
        $existingTask = new Task([
            'id' => 1,
            'title' => 'Tarefa Original',
            'description' => 'Descrição original',
            'status' => 'pending'
        ]);

        // Dados de atualização
        $updateData = [
            'title' => 'Tarefa Atualizada',
            'status' => 'in_progress'
        ];

        // Tarefa atualizada
        $updatedTask = new Task(array_merge($existingTask->toArray(), $updateData));

        // Configura mocks
        $this->taskRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($existingTask);

        $this->taskRepositoryMock
            ->shouldReceive('update')
            ->once()
            ->with(1, $updateData)
            ->andReturn($updatedTask);

        $this->logServiceMock
            ->shouldReceive('logTaskUpdated')
            ->once()
            ->with($updatedTask, $existingTask->toArray(), $updateData);

        // Executa teste
        $result = $this->taskService->updateTask(1, $updateData);

        // Verifica resultado
        $this->assertInstanceOf(Task::class, $result);
        $this->assertEquals('Tarefa Atualizada', $result->title);
        $this->assertEquals('in_progress', $result->status);
    }

    /**
     * Testa atualização de tarefa inexistente
     * 
     * @return void
     */
    public function test_cannot_update_nonexistent_task(): void
    {
        // Configura mock
        $this->taskRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        // Executa teste
        $result = $this->taskService->updateTask(999, ['title' => 'Teste']);

        // Verifica resultado
        $this->assertNull($result);
    }

    /**
     * Testa exclusão de tarefa existente
     * 
     * @return void
     */
    public function test_can_delete_existing_task(): void
    {
        // Tarefa existente
        $task = new Task([
            'id' => 1,
            'title' => 'Tarefa para Excluir',
            'status' => 'pending'
        ]);

        // Configura mocks
        $this->taskRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($task);

        $this->taskRepositoryMock
            ->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        $this->logServiceMock
            ->shouldReceive('logTaskDeleted')
            ->once()
            ->with($task);

        // Executa teste
        $result = $this->taskService->deleteTask(1);

        // Verifica resultado
        $this->assertTrue($result);
    }

    /**
     * Testa exclusão de tarefa inexistente
     * 
     * @return void
     */
    public function test_cannot_delete_nonexistent_task(): void
    {
        // Configura mock
        $this->taskRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        // Executa teste
        $result = $this->taskService->deleteTask(999);

        // Verifica resultado
        $this->assertFalse($result);
    }

    /**
     * Testa listagem de tarefas
     * 
     * @return void
     */
    public function test_can_list_tasks(): void
    {
        // Tarefas de teste
        $tasks = collect([
            new Task(['id' => 1, 'title' => 'Tarefa 1', 'status' => 'pending']),
            new Task(['id' => 2, 'title' => 'Tarefa 2', 'status' => 'completed'])
        ]);

        // Configura mock
        $this->taskRepositoryMock
            ->shouldReceive('findAll')
            ->once()
            ->with([])
            ->andReturn($tasks);

        // Executa teste
        $result = $this->taskService->listTasks();

        // Verifica resultado
        $this->assertCount(2, $result);
        $this->assertEquals('Tarefa 1', $result->first()->title);
    }

    /**
     * Testa listagem de tarefas com filtros
     * 
     * @return void
     */
    public function test_can_list_tasks_with_filters(): void
    {
        // Filtros
        $filters = ['status' => 'pending'];

        // Tarefas filtradas
        $tasks = collect([
            new Task(['id' => 1, 'title' => 'Tarefa Pendente', 'status' => 'pending'])
        ]);

        // Configura mock
        $this->taskRepositoryMock
            ->shouldReceive('findAll')
            ->once()
            ->with($filters)
            ->andReturn($tasks);

        // Executa teste
        $result = $this->taskService->listTasks($filters);

        // Verifica resultado
        $this->assertCount(1, $result);
        $this->assertEquals('pending', $result->first()->status);
    }

    /**
     * Testa obtenção de estatísticas de tarefas
     * 
     * @return void
     */
    public function test_can_get_task_statistics(): void
    {
        // Configura mocks
        $this->taskRepositoryMock
            ->shouldReceive('countByStatus')
            ->once()
            ->with(null)
            ->andReturn(10);

        $this->taskRepositoryMock
            ->shouldReceive('countByStatus')
            ->once()
            ->with('pending')
            ->andReturn(5);

        $this->taskRepositoryMock
            ->shouldReceive('countByStatus')
            ->once()
            ->with('in_progress')
            ->andReturn(3);

        $this->taskRepositoryMock
            ->shouldReceive('countByStatus')
            ->once()
            ->with('completed')
            ->andReturn(2);

        // Executa teste
        $result = $this->taskService->getTaskStatistics();

        // Verifica resultado
        $this->assertEquals([
            'total' => 10,
            'pending' => 5,
            'in_progress' => 3,
            'completed' => 2
        ], $result);
    }
}



