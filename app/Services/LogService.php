<?php

namespace App\Services;

use App\Contracts\Repositories\LogRepositoryInterface;
use App\Contracts\Services\LogServiceInterface;
use App\Models\Log;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

/**
 * LogService
 * 
 * Implementação do serviço de logs.
 * Segue o princípio de Responsabilidade Única (SRP) do SOLID.
 * 
 * Responsável apenas pela lógica de negócio relacionada a logs.
 */
class LogService implements LogServiceInterface
{
    /**
     * Repositório de logs
     * 
     * @var LogRepositoryInterface
     */
    protected LogRepositoryInterface $logRepository;

    /**
     * Construtor
     * 
     * @param LogRepositoryInterface $logRepository
     */
    public function __construct(LogRepositoryInterface $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    /**
     * Lista logs recentes
     * 
     * @param int $limit Limite de registros
     * @return Collection
     */
    public function listRecentLogs(int $limit = 30): Collection
    {
        return $this->logRepository->findRecent($limit);
    }

    /**
     * Lista logs por entidade
     * 
     * @param string $entityType Tipo da entidade
     * @param string|null $entityId ID da entidade (opcional)
     * @param int $limit Limite de registros
     * @return Collection
     */
    public function listLogsByEntity(string $entityType, ?string $entityId = null, int $limit = 30): Collection
    {
        return $this->logRepository->findByEntity($entityType, $entityId, $limit);
    }

    /**
     * Busca um log específico por ID
     * 
     * @param string $id
     * @return Log|null
     */
    public function getLogById(string $id): ?Log
    {
        return $this->logRepository->findById($id);
    }

    /**
     * Registra log de criação de tarefa
     * 
     * @param Task $task
     * @return void
     */
    public function logTaskCreated(Task $task): void
    {
        $this->processLogAsync(
            Log::ACTION_CREATED,
            Log::ENTITY_TYPE_TASK,
            (string) $task->id,
            $task->toArray()
        );
    }

    /**
     * Registra log de atualização de tarefa
     * 
     * @param Task $task
     * @param array $oldData
     * @param array $newData
     * @return void
     */
    public function logTaskUpdated(Task $task, array $oldData, array $newData): void
    {
        $this->processLogAsync(
            Log::ACTION_UPDATED,
            Log::ENTITY_TYPE_TASK,
            (string) $task->id,
            [
                'old' => $oldData,
                'new' => $newData,
            ]
        );
    }

    /**
     * Registra log de exclusão de tarefa
     * 
     * @param Task $task
     * @return void
     */
    public function logTaskDeleted(Task $task): void
    {
        $this->processLogAsync(
            Log::ACTION_DELETED,
            Log::ENTITY_TYPE_TASK,
            (string) $task->id,
            $task->toArray()
        );
    }

    /**
     * Processa logs de forma síncrona
     * 
     * @param string $action
     * @param string $entityType
     * @param string $entityId
     * @param array $data
     * @return void
     */
    public function processLogAsync(string $action, string $entityType, string $entityId, array $data): void
    {
        try {
            // Cria o log baseado na ação
            switch ($action) {
                case Log::ACTION_CREATED:
                    $this->logRepository->createCreatedLog(
                        $entityType,
                        $entityId,
                        $data
                    );
                    break;

                case Log::ACTION_UPDATED:
                    $this->logRepository->createUpdatedLog(
                        $entityType,
                        $entityId,
                        $data['old'] ?? [],
                        $data['new'] ?? []
                    );
                    break;

                case Log::ACTION_DELETED:
                    $this->logRepository->createDeletedLog(
                        $entityType,
                        $entityId,
                        $data
                    );
                    break;

                default:
                    // Log genérico para ações não mapeadas
                    $this->logRepository->create([
                        'entity_type' => $entityType,
                        'entity_id' => $entityId,
                        'action' => $action,
                        'data' => $data,
                    ]);
                    break;
            }

            \Illuminate\Support\Facades\Log::info('Log processado com sucesso', [
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao processar log', [
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-lança a exceção para que o erro seja propagado
            throw $e;
        }
    }
}


