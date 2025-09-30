<?php

namespace App\Services;

use App\Contracts\Repositories\TaskRepositoryInterface;
use App\Contracts\Services\LogServiceInterface;
use App\Contracts\Services\TaskServiceInterface;
use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * TaskService
 * 
 * Implementação do serviço de tarefas.
 * Segue o princípio de Responsabilidade Única (SRP) do SOLID.
 * 
 * Responsável apenas pela lógica de negócio relacionada a tarefas.
 */
class TaskService implements TaskServiceInterface
{
    /**
     * Repositório de tarefas
     * 
     * @var TaskRepositoryInterface
     */
    protected TaskRepositoryInterface $taskRepository;

    /**
     * Serviço de logs
     * 
     * @var LogServiceInterface|null
     */
    protected ?LogServiceInterface $logService;

    /**
     * Construtor
     * 
     * @param TaskRepositoryInterface $taskRepository
     * @param LogServiceInterface|null $logService
     */
    public function __construct(
        TaskRepositoryInterface $taskRepository,
        ?LogServiceInterface $logService = null
    ) {
        $this->taskRepository = $taskRepository;
        $this->logService = $logService;
    }

    /**
     * Lista todas as tarefas
     * 
     * @param array $filters Filtros opcionais
     * @return Collection
     */
    public function listTasks(array $filters = []): Collection
    {
        try {
            return $this->taskRepository->findAll($filters);
        } catch (\Exception $e) {
            // Retorna uma coleção vazia se houver erro de conexão com o banco
            return new \Illuminate\Database\Eloquent\Collection([]);
        }
    }

    /**
     * Lista tarefas com paginação
     * 
     * @param int $perPage Itens por página
     * @param array $filters Filtros opcionais
     * @return LengthAwarePaginator
     */
    public function listPaginatedTasks(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->taskRepository->findPaginated($perPage, $filters);
    }

    /**
     * Busca uma tarefa por ID
     * 
     * @param int $id
     * @return Task|null
     */
    public function getTaskById(int $id): ?Task
    {
        return $this->taskRepository->findById($id);
    }

    /**
     * Cria uma nova tarefa
     * 
     * @param array $data
     * @return Task
     * @throws ValidationException
     */
    public function createTask(array $data): Task
    {
        // Valida os dados
        $validatedData = $this->validateTaskData($data);

        // Cria a tarefa
        $task = $this->taskRepository->create($validatedData);

        // Registra log de criação (assíncrono) se o serviço estiver disponível
        if ($this->logService) {
            $this->logService->logTaskCreated($task);
        }

        return $task;
    }

    /**
     * Atualiza uma tarefa existente
     * 
     * @param int $id
     * @param array $data
     * @return Task|null
     * @throws ValidationException
     */
    public function updateTask(int $id, array $data): ?Task
    {
        // Busca a tarefa existente
        $existingTask = $this->taskRepository->findById($id);
        
        if (!$existingTask) {
            return null;
        }

        // Valida os dados
        $validatedData = $this->validateTaskData($data, $id);

        // Armazena dados antigos para log
        $oldData = $existingTask->toArray();

        // Atualiza a tarefa
        $updatedTask = $this->taskRepository->update($id, $validatedData);

        if ($updatedTask && $this->logService) {
            // Registra log de atualização (assíncrono) se o serviço estiver disponível
            $this->logService->logTaskUpdated($updatedTask, $oldData, $validatedData);
        }

        return $updatedTask;
    }

    /**
     * Exclui uma tarefa
     * 
     * @param int $id
     * @return bool
     */
    public function deleteTask(int $id): bool
    {
        // Busca a tarefa existente
        $task = $this->taskRepository->findById($id);
        
        if (!$task) {
            return false;
        }

        // Registra log de exclusão (assíncrono) se o serviço estiver disponível
        if ($this->logService) {
            $this->logService->logTaskDeleted($task);
        }

        // Exclui a tarefa
        return $this->taskRepository->delete($id);
    }

    /**
     * Valida dados de tarefa
     * 
     * @param array $data
     * @param int|null $id ID da tarefa para validação de atualização
     * @return array
     * @throws ValidationException
     */
    public function validateTaskData(array $data, ?int $id = null): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:' . implode(',', Task::getStatuses()),
        ];

        // Para atualização, todos os campos são opcionais
        if ($id) {
            $rules = array_map(function ($rule) {
                return str_replace('required|', '', $rule);
            }, $rules);
        }

        $validator = Validator::make($data, $rules, [
            'title.required' => 'O título é obrigatório.',
            'title.string' => 'O título deve ser uma string.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'description.string' => 'A descrição deve ser uma string.',
            'status.required' => 'O status é obrigatório.',
            'status.in' => 'O status deve ser: ' . implode(', ', Task::getStatuses()),
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Obtém estatísticas de tarefas
     * 
     * @return array
     */
    public function getTaskStatistics(): array
    {
        return [
            'total' => $this->taskRepository->countByStatus(),
            'pending' => $this->taskRepository->countByStatus(Task::STATUS_PENDING),
            'in_progress' => $this->taskRepository->countByStatus(Task::STATUS_IN_PROGRESS),
            'completed' => $this->taskRepository->countByStatus(Task::STATUS_COMPLETED),
        ];
    }
}


