<?php

namespace App\Repositories;

use App\Contracts\Repositories\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * TaskRepository
 * 
 * Implementação do repositório de tarefas.
 * Segue o princípio de Responsabilidade Única (SRP) do SOLID.
 * 
 * Responsável apenas pela persistência de dados de tarefas.
 */
class TaskRepository implements TaskRepositoryInterface
{
    /**
     * Model de tarefa
     * 
     * @var Task
     */
    protected Task $model;

    /**
     * Construtor
     * 
     * @param Task|null $model
     */
    public function __construct(?Task $model = null)
    {
        $this->model = $model ?? new Task();
    }

    /**
     * Busca todas as tarefas
     * 
     * @param array $filters Filtros opcionais
     * @return Collection
     */
    public function findAll(array $filters = []): Collection
    {
        $query = $this->model->newQuery();

        // Aplica filtro por status se fornecido
        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        // Ordena por data de criação (mais recentes primeiro)
        $query->orderBy('created_at', 'desc');

        return $query->get();
    }

    /**
     * Busca tarefas com paginação
     * 
     * @param int $perPage Itens por página
     * @param array $filters Filtros opcionais
     * @return LengthAwarePaginator
     */
    public function findPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        // Aplica filtro por status se fornecido
        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        // Ordena por data de criação (mais recentes primeiro)
        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Busca uma tarefa por ID
     * 
     * @param int $id
     * @return Task|null
     */
    public function findById(int $id): ?Task
    {
        return $this->model->find($id);
    }

    /**
     * Cria uma nova tarefa
     * 
     * @param array $data
     * @return Task
     */
    public function create(array $data): Task
    {
        return $this->model->create($data);
    }

    /**
     * Atualiza uma tarefa existente
     * 
     * @param int $id
     * @param array $data
     * @return Task|null
     */
    public function update(int $id, array $data): ?Task
    {
        $task = $this->findById($id);
        
        if (!$task) {
            return null;
        }

        $task->update($data);
        
        return $task->fresh();
    }

    /**
     * Exclui uma tarefa
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $task = $this->findById($id);
        
        if (!$task) {
            return false;
        }

        return $task->delete();
    }

    /**
     * Verifica se uma tarefa existe
     * 
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    /**
     * Busca tarefas por status
     * 
     * @param string $status
     * @return Collection
     */
    public function findByStatus(string $status): Collection
    {
        return $this->model->byStatus($status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Conta tarefas por status
     * 
     * @param string|null $status
     * @return int
     */
    public function countByStatus(?string $status = null): int
    {
        $query = $this->model->newQuery();
        
        if ($status) {
            $query->byStatus($status);
        }
        
        return $query->count();
    }
}

