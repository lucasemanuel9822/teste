<?php

namespace Tests\Unit;

use App\Contracts\Repositories\LogRepositoryInterface;
use App\Jobs\ProcessLogJob;
use App\Models\Log;
use App\Models\Task;
use App\Services\LogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

/**
 * LogServiceTest
 * 
 * Testes unitários para o LogService.
 * Testa a lógica de negócio relacionada a logs.
 */
class LogServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Repositório mock de logs
     * 
     * @var Mockery\MockInterface
     */
    protected $logRepositoryMock;

    /**
     * Serviço de logs
     * 
     * @var LogService
     */
    protected LogService $logService;

    /**
     * Configuração inicial dos testes
     * 
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Cria mock
        $this->logRepositoryMock = Mockery::mock(LogRepositoryInterface::class);

        // Cria instância do serviço
        $this->logService = new LogService($this->logRepositoryMock);
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
     * Testa listagem de logs recentes
     * 
     * @return void
     */
    public function test_can_list_recent_logs(): void
    {
        // Logs de teste
        $logs = collect([
            new Log(['action' => 'created', 'entity_type' => 'task']),
            new Log(['action' => 'updated', 'entity_type' => 'task'])
        ]);

        // Configura mock
        $this->logRepositoryMock
            ->shouldReceive('findRecent')
            ->once()
            ->with(30)
            ->andReturn($logs);

        // Executa teste
        $result = $this->logService->listRecentLogs(30);

        // Verifica resultado
        $this->assertCount(2, $result);
    }

    /**
     * Testa listagem de logs por entidade
     * 
     * @return void
     */
    public function test_can_list_logs_by_entity(): void
    {
        // Logs de teste
        $logs = collect([
            new Log(['action' => 'created', 'entity_type' => 'task', 'entity_id' => '1'])
        ]);

        // Configura mock
        $this->logRepositoryMock
            ->shouldReceive('findByEntity')
            ->once()
            ->with('task', '1', 30)
            ->andReturn($logs);

        // Executa teste
        $result = $this->logService->listLogsByEntity('task', '1', 30);

        // Verifica resultado
        $this->assertCount(1, $result);
    }

    /**
     * Testa busca de log por ID
     * 
     * @return void
     */
    public function test_can_get_log_by_id(): void
    {
        // Log de teste
        $log = new Log(['_id' => '507f1f77bcf86cd799439011', 'action' => 'created']);

        // Configura mock
        $this->logRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with('507f1f77bcf86cd799439011')
            ->andReturn($log);

        // Executa teste
        $result = $this->logService->getLogById('507f1f77bcf86cd799439011');

        // Verifica resultado
        $this->assertInstanceOf(Log::class, $result);
        $this->assertEquals('507f1f77bcf86cd799439011', $result->_id);
    }

    /**
     * Testa registro de log de criação de tarefa
     * 
     * @return void
     */
    public function test_can_log_task_created(): void
    {
        // Tarefa de teste
        $task = new Task([
            'id' => 1,
            'title' => 'Tarefa de Teste',
            'status' => 'pending'
        ]);

        // Configura fila para não executar jobs
        Queue::fake();

        // Executa teste
        $this->logService->logTaskCreated($task);

        // Verifica se o job foi despachado
        Queue::assertPushed(ProcessLogJob::class, function ($job) {
            return $job->action === 'created' &&
                   $job->entityType === 'task' &&
                   $job->entityId === '1';
        });
    }

    /**
     * Testa registro de log de atualização de tarefa
     * 
     * @return void
     */
    public function test_can_log_task_updated(): void
    {
        // Tarefa de teste
        $task = new Task([
            'id' => 1,
            'title' => 'Tarefa Atualizada',
            'status' => 'in_progress'
        ]);

        // Dados antigos e novos
        $oldData = ['title' => 'Tarefa Original', 'status' => 'pending'];
        $newData = ['title' => 'Tarefa Atualizada', 'status' => 'in_progress'];

        // Configura fila para não executar jobs
        Queue::fake();

        // Executa teste
        $this->logService->logTaskUpdated($task, $oldData, $newData);

        // Verifica se o job foi despachado
        Queue::assertPushed(ProcessLogJob::class, function ($job) {
            return $job->action === 'updated' &&
                   $job->entityType === 'task' &&
                   $job->entityId === '1';
        });
    }

    /**
     * Testa registro de log de exclusão de tarefa
     * 
     * @return void
     */
    public function test_can_log_task_deleted(): void
    {
        // Tarefa de teste
        $task = new Task([
            'id' => 1,
            'title' => 'Tarefa para Excluir',
            'status' => 'pending'
        ]);

        // Configura fila para não executar jobs
        Queue::fake();

        // Executa teste
        $this->logService->logTaskDeleted($task);

        // Verifica se o job foi despachado
        Queue::assertPushed(ProcessLogJob::class, function ($job) {
            return $job->action === 'deleted' &&
                   $job->entityType === 'task' &&
                   $job->entityId === '1';
        });
    }

    /**
     * Testa processamento assíncrono de logs
     * 
     * @return void
     */
    public function test_can_process_log_async(): void
    {
        // Configura fila para não executar jobs
        Queue::fake();

        // Dados do log
        $action = 'created';
        $entityType = 'task';
        $entityId = '1';
        $data = ['title' => 'Tarefa de Teste'];

        // Executa teste
        $this->logService->processLogAsync($action, $entityType, $entityId, $data);

        // Verifica se o job foi despachado
        Queue::assertPushed(ProcessLogJob::class, function ($job) use ($action, $entityType, $entityId, $data) {
            return $job->action === $action &&
                   $job->entityType === $entityType &&
                   $job->entityId === $entityId &&
                   $job->data === $data;
        });
    }
}



