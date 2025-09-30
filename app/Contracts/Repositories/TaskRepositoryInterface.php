<?php

namespace App\Contracts\Repositories;

use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface TaskRepositoryInterface
 * 
 * Define o contrato para operações de repositório de tarefas.
 * Segue o princípio de Inversão de Dependência (DIP) do SOLID.
 */
interface TaskRepositoryInterface
{
    /**
     * Busca todas as tarefas
     * 
     * @param array $filters Filtros opcionais
     * @return Collection
     */
    public function findAll(array $filters = []): Collection;

    /**
     * Busca tarefas com paginação
     * 
     * @param int $perPage Itens por página
     * @param array $filters Filtros opcionais
     * @return LengthAwarePaginator
     */
    public function findPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Busca uma tarefa por ID
     * 
     * @param int $id
     * @return Task|null
     */
    public function findById(int $id): ?Task;

    /**
     * Cria uma nova tarefa
     * 
     * @param array $data
     * @return Task
     */
    public function create(array $data): Task;

    /**
     * Atualiza uma tarefa existente
     * 
     * @param int $id
     * @param array $data
     * @return Task|null
     */
    public function update(int $id, array $data): ?Task;

    /**
     * Exclui uma tarefa
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Verifica se uma tarefa existe
     * 
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool;

    /**
     * Busca tarefas por status
     * 
     * @param string $status
     * @return Collection
     */
    public function findByStatus(string $status): Collection;

    /**
     * Conta tarefas por status
     * 
     * @param string|null $status
     * @return int
     */
    public function countByStatus(?string $status = null): int;
}

