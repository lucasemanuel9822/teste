<?php

namespace App\Repositories;

use App\Contracts\Repositories\LogRepositoryInterface;
use App\Models\Log;
use Illuminate\Database\Eloquent\Collection;

/**
 * LogRepository
 * 
 * Implementação do repositório de logs.
 * Segue o princípio de Responsabilidade Única (SRP) do SOLID.
 * 
 * Responsável apenas pela persistência de dados de logs no MongoDB.
 */
class LogRepository implements LogRepositoryInterface
{
    /**
     * Model de log
     * 
     * @var Log
     */
    protected Log $model;

    /**
     * Construtor
     * 
     * @param Log $model
     */
    public function __construct(Log $model)
    {
        $this->model = $model;
    }

    /**
     * Busca logs recentes
     * 
     * @param int $limit Limite de registros
     * @return Collection
     */
    public function findRecent(int $limit = 30): Collection
    {
        return $this->model->recent($limit)->get();
    }

    /**
     * Busca logs por entidade
     * 
     * @param string $entityType Tipo da entidade
     * @param string|null $entityId ID da entidade (opcional)
     * @param int $limit Limite de registros
     * @return Collection
     */
    public function findByEntity(string $entityType, ?string $entityId = null, int $limit = 30): Collection
    {
        $query = $this->model->byEntity($entityType, $entityId);
        
        return $query->recent($limit)->get();
    }

    /**
     * Busca um log específico por ID
     * 
     * @param string $id
     * @return Log|null
     */
    public function findById(string $id): ?Log
    {
        return $this->model->find($id);
    }

    /**
     * Cria um novo log
     * 
     * @param array $data
     * @return Log
     */
    public function create(array $data): Log
    {
        return $this->model->create($data);
    }

    /**
     * Cria log de criação de entidade
     * 
     * @param string $entityType
     * @param string $entityId
     * @param array $data
     * @return Log
     */
    public function createCreatedLog(string $entityType, string $entityId, array $data): Log
    {
        return Log::createCreatedLog($entityType, $entityId, $data);
    }

    /**
     * Cria log de atualização de entidade
     * 
     * @param string $entityType
     * @param string $entityId
     * @param array $oldData
     * @param array $newData
     * @return Log
     */
    public function createUpdatedLog(string $entityType, string $entityId, array $oldData, array $newData): Log
    {
        return Log::createUpdatedLog($entityType, $entityId, $oldData, $newData);
    }

    /**
     * Cria log de exclusão de entidade
     * 
     * @param string $entityType
     * @param string $entityId
     * @param array $data
     * @return Log
     */
    public function createDeletedLog(string $entityType, string $entityId, array $data): Log
    {
        return Log::createDeletedLog($entityType, $entityId, $data);
    }

    /**
     * Conta logs por entidade
     * 
     * @param string $entityType
     * @param string|null $entityId
     * @return int
     */
    public function countByEntity(string $entityType, ?string $entityId = null): int
    {
        $query = $this->model->byEntity($entityType, $entityId);
        
        return $query->count();
    }
}

