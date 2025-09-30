<?php

namespace App\Contracts\Repositories;

use App\Models\Log;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface LogRepositoryInterface
 * 
 * Define o contrato para operações de repositório de logs.
 * Segue o princípio de Inversão de Dependência (DIP) do SOLID.
 */
interface LogRepositoryInterface
{
    /**
     * Busca logs recentes
     * 
     * @param int $limit Limite de registros
     * @return Collection
     */
    public function findRecent(int $limit = 30): Collection;

    /**
     * Busca logs por entidade
     * 
     * @param string $entityType Tipo da entidade
     * @param string|null $entityId ID da entidade (opcional)
     * @param int $limit Limite de registros
     * @return Collection
     */
    public function findByEntity(string $entityType, ?string $entityId = null, int $limit = 30): Collection;

    /**
     * Busca um log específico por ID
     * 
     * @param string $id
     * @return Log|null
     */
    public function findById(string $id): ?Log;

    /**
     * Cria um novo log
     * 
     * @param array $data
     * @return Log
     */
    public function create(array $data): Log;

    /**
     * Cria log de criação de entidade
     * 
     * @param string $entityType
     * @param string $entityId
     * @param array $data
     * @return Log
     */
    public function createCreatedLog(string $entityType, string $entityId, array $data): Log;

    /**
     * Cria log de atualização de entidade
     * 
     * @param string $entityType
     * @param string $entityId
     * @param array $oldData
     * @param array $newData
     * @return Log
     */
    public function createUpdatedLog(string $entityType, string $entityId, array $oldData, array $newData): Log;

    /**
     * Cria log de exclusão de entidade
     * 
     * @param string $entityType
     * @param string $entityId
     * @param array $data
     * @return Log
     */
    public function createDeletedLog(string $entityType, string $entityId, array $data): Log;

    /**
     * Conta logs por entidade
     * 
     * @param string $entityType
     * @param string|null $entityId
     * @return int
     */
    public function countByEntity(string $entityType, ?string $entityId = null): int;
}

