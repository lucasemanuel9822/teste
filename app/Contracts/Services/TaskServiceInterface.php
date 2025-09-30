<?php

namespace App\Contracts\Services;

use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface TaskServiceInterface
 * 
 * Define o contrato para operações de serviço de tarefas.
 * Segue o princípio de Inversão de Dependência (DIP) do SOLID.
 */
interface TaskServiceInterface
{
    /**
     * Lista todas as tarefas
     * 
     * @param array $filters Filtros opcionais
     * @return Collection
     */
    public function listTasks(array $filters = []): Collection;

    /**
     * Lista tarefas com paginação
     * 
     * @param int $perPage Itens por página
     * @param array $filters Filtros opcionais
     * @return LengthAwarePaginator
     */
    public function listPaginatedTasks(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Busca uma tarefa por ID
     * 
     * @param int $id
     * @return Task|null
     */
    public function getTaskById(int $id): ?Task;

    /**
     * Cria uma nova tarefa
     * 
     * @param array $data
     * @return Task
     */
    public function createTask(array $data): Task;

    /**
     * Atualiza uma tarefa existente
     * 
     * @param int $id
     * @param array $data
     * @return Task|null
     */
    public function updateTask(int $id, array $data): ?Task;

    /**
     * Exclui uma tarefa
     * 
     * @param int $id
     * @return bool
     */
    public function deleteTask(int $id): bool;

    /**
     * Valida dados de tarefa
     * 
     * @param array $data
     * @param int|null $id ID da tarefa para validação de atualização
     * @return array
     */
    public function validateTaskData(array $data, ?int $id = null): array;

    /**
     * Obtém estatísticas de tarefas
     * 
     * @return array
     */
    public function getTaskStatistics(): array;
}

