<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

/**
 * Model Log
 * 
 * Representa um log de evento no sistema.
 * Utiliza MongoDB através do pacote jenssegers/mongodb.
 * 
 * @property string $_id
 * @property string $entity_type
 * @property string $entity_id
 * @property string $action
 * @property array $data
 * @property \Carbon\Carbon $created_at
 */
class Log extends Model
{
    /**
     * Nome da coleção no MongoDB
     */
    protected $collection = 'logs';
    
    /**
     * Conexão do banco de dados
     */
    protected $connection = 'mongodb';

    /**
     * Campos que podem ser preenchidos em massa
     */
    protected $fillable = [
        'entity_type',
        'entity_id',
        'action',
        'data',
    ];

    /**
     * Tipos de dados para casting automático
     */
    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Ações possíveis para logs
     */
    public const ACTION_CREATED = 'created';
    public const ACTION_UPDATED = 'updated';
    public const ACTION_DELETED = 'deleted';

    /**
     * Tipos de entidades que podem ser logadas
     */
    public const ENTITY_TYPE_TASK = 'task';

    /**
     * Retorna todas as ações possíveis
     * 
     * @return array
     */
    public static function getActions(): array
    {
        return [
            self::ACTION_CREATED,
            self::ACTION_UPDATED,
            self::ACTION_DELETED,
        ];
    }

    /**
     * Scope para filtrar logs por entidade
     * 
     * @param \MongoDB\Laravel\Eloquent\Builder $query
     * @param string $entityType
     * @param string|null $entityId
     * @return \MongoDB\Laravel\Eloquent\Builder
     */
    public function scopeByEntity($query, string $entityType, ?string $entityId = null)
    {
        $query = $query->where('entity_type', $entityType);
        
        if ($entityId) {
            $query = $query->where('entity_id', $entityId);
        }
        
        return $query;
    }

    /**
     * Scope para obter logs recentes
     * 
     * @param \MongoDB\Laravel\Eloquent\Builder $query
     * @param int $limit
     * @return \MongoDB\Laravel\Eloquent\Builder
     */
    public function scopeRecent($query, int $limit = 30)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Cria um log de criação
     * 
     * @param string $entityType
     * @param string $entityId
     * @param array $data
     * @return static
     */
    public static function createCreatedLog(string $entityType, string $entityId, array $data): self
    {
        return self::create([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => self::ACTION_CREATED,
            'data' => $data,
        ]);
    }

    /**
     * Cria um log de atualização
     * 
     * @param string $entityType
     * @param string $entityId
     * @param array $oldData
     * @param array $newData
     * @return static
     */
    public static function createUpdatedLog(string $entityType, string $entityId, array $oldData, array $newData): self
    {
        return self::create([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => self::ACTION_UPDATED,
            'data' => [
                'old' => $oldData,
                'new' => $newData,
            ],
        ]);
    }

    /**
     * Cria um log de exclusão
     * 
     * @param string $entityType
     * @param string $entityId
     * @param array $data
     * @return static
     */
    public static function createDeletedLog(string $entityType, string $entityId, array $data): self
    {
        return self::create([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => self::ACTION_DELETED,
            'data' => $data,
        ]);
    }
}

