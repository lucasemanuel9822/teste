<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model Task
 * 
 * Representa uma tarefa no sistema.
 * Utiliza Eloquent ORM para interação com MySQL.
 * 
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Task extends Model
{
    use HasFactory;

    /**
     * Nome da tabela no banco de dados
     */
    protected $table = 'tasks';

    /**
     * Campos que podem ser preenchidos em massa
     */
    protected $fillable = [
        'title',
        'description',
        'status',
    ];

    /**
     * Tipos de dados para casting automático
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Status possíveis para uma tarefa
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    /**
     * Retorna todos os status possíveis
     * 
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED,
        ];
    }

    /**
     * Scope para filtrar tarefas por status
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Verifica se a tarefa está pendente
     * 
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Verifica se a tarefa está em progresso
     * 
     * @return bool
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Verifica se a tarefa está concluída
     * 
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}

