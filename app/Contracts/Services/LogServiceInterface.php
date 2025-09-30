<?php

namespace App\Contracts\Services;

use App\Models\Log;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface LogServiceInterface
 * 
 * Define o contrato para operações de serviço de logs.
 * Segue o princípio de Inversão de Dependência (DIP) do SOLID.
 */
interface LogServiceInterface
{
    /**
     * Lista logs recentes
     * 
     * @param int $limit Limite de registros
     * @return Collection
     */
    public function listRecentLogs(int $limit = 30): Collection;

    /**
     * Lista logs por entidade
     * 
     * @param string $entityType Tipo da entidade
     * @param string|null $entityId ID da entidade (opcional)
     * @param int $limit Limite de registros
     * @return Collection
     */
    public function listLogsByEntity(string $entityType, ?string $entityId = null, int $limit = 30): Collection;

    /**
     * Busca um log específico por ID
     * 
     * @param string $id
     * @return Log|null
     */
    public function getLogById(string $id): ?Log;

    /**
     * Registra log de criação de tarefa
     * 
     * @param Task $task
     * @return void
     */
    public function logTaskCreated(Task $task): void;

    /**
     * Registra log de atualização de tarefa
     * 
     * @param Task $task
     * @param array $oldData
     * @param array $newData
     * @return void
     */
    public function logTaskUpdated(Task $task, array $oldData, array $newData): void;

    /**
     * Registra log de exclusão de tarefa
     * 
     * @param Task $task
     * @return void
     */
    public function logTaskDeleted(Task $task): void;

    /**
     * Processa logs de forma assíncrona
     * 
     * @param string $action
     * @param string $entityType
     * @param string $entityId
     * @param array $data
     * @return void
     */
    public function processLogAsync(string $action, string $entityType, string $entityId, array $data): void;
}

