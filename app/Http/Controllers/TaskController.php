<?php

namespace App\Http\Controllers;

use App\Contracts\Services\TaskServiceInterface;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * TaskController
 * 
 * Controller para gerenciamento de tarefas.
 * Implementa endpoints RESTful conforme especificação do projeto.
 * 
 * Endpoints implementados:
 * - POST /tasks: Criar nova tarefa
 * - GET /tasks: Listar tarefas (com filtros)
 * - GET /tasks/{id}: Buscar tarefa por ID
 * - PUT /tasks/{id}: Atualizar tarefa
 * - DELETE /tasks/{id}: Excluir tarefa
 */
class TaskController extends Controller
{
    /**
     * Serviço de tarefas
     * 
     * @var TaskServiceInterface
     */
    protected TaskServiceInterface $taskService;

    /**
     * Construtor
     * 
     * @param TaskServiceInterface $taskService
     */
    public function __construct(TaskServiceInterface $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Lista todas as tarefas
     * 
     * @param Request $request
     * @return AnonymousResourceCollection
     * 
     * @OA\Get(
     *     path="/tasks",
     *     summary="Lista todas as tarefas",
     *     description="Retorna uma lista de tarefas com suporte a filtros por status",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filtrar por status da tarefa",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pending", "in_progress", "completed"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tarefas retornada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Task"))
     *         )
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['status']);
        
        // Valida filtro de status se fornecido
        if (isset($filters['status'])) {
            $validator = Validator::make($filters, [
                'status' => 'in:' . implode(',', Task::getStatuses())
            ]);
            
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        }

        $tasks = $this->taskService->listTasks($filters);
        
        return TaskResource::collection($tasks);
    }

    /**
     * Cria uma nova tarefa
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * @OA\Post(
     *     path="/tasks",
     *     summary="Cria uma nova tarefa",
     *     description="Cria uma nova tarefa no sistema",
     *     tags={"Tasks"},
     *     security={{"apiKey": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "status"},
     *             @OA\Property(property="title", type="string", example="Implementar autenticação"),
     *             @OA\Property(property="description", type="string", example="Criar sistema de autenticação usando API Key"),
     *             @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed"}, example="pending")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tarefa criada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado - API Key inválida"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados de validação inválidos"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $task = $this->taskService->createTask($request->all());
            
            return response()->json([
                'message' => 'Tarefa criada com sucesso',
                'data' => new TaskResource($task)
            ], 201);
            
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Dados de validação inválidos',
                'message' => 'Os dados fornecidos não atendem aos requisitos',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro interno do servidor',
                'message' => 'Ocorreu um erro inesperado ao criar a tarefa'
            ], 500);
        }
    }

    /**
     * Busca uma tarefa por ID
     * 
     * @param int $id
     * @return JsonResponse
     * 
     * @OA\Get(
     *     path="/tasks/{id}",
     *     summary="Busca tarefa por ID",
     *     description="Retorna os dados de uma tarefa específica",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tarefa",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tarefa encontrada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tarefa não encontrada"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $task = $this->taskService->getTaskById($id);
        
        if (!$task) {
            return response()->json([
                'error' => 'Tarefa não encontrada',
                'message' => 'A tarefa solicitada não existe no sistema'
            ], 404);
        }
        
        return response()->json([
            'data' => new TaskResource($task)
        ]);
    }

    /**
     * Atualiza uma tarefa existente
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * 
     * @OA\Put(
     *     path="/tasks/{id}",
     *     summary="Atualiza uma tarefa",
     *     description="Atualiza os dados de uma tarefa existente",
     *     tags={"Tasks"},
     *     security={{"apiKey": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tarefa",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Implementar autenticação"),
     *             @OA\Property(property="description", type="string", example="Criar sistema de autenticação usando API Key"),
     *             @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed"}, example="in_progress")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tarefa atualizada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado - API Key inválida"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tarefa não encontrada"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados de validação inválidos"
     *     )
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $task = $this->taskService->updateTask($id, $request->all());
            
            if (!$task) {
                return response()->json([
                    'error' => 'Tarefa não encontrada',
                    'message' => 'A tarefa solicitada não existe no sistema'
                ], 404);
            }
            
            return response()->json([
                'message' => 'Tarefa atualizada com sucesso',
                'data' => new TaskResource($task)
            ]);
            
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Dados de validação inválidos',
                'message' => 'Os dados fornecidos não atendem aos requisitos',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro interno do servidor',
                'message' => 'Ocorreu um erro inesperado ao atualizar a tarefa'
            ], 500);
        }
    }

    /**
     * Exclui uma tarefa
     * 
     * @param int $id
     * @return JsonResponse
     * 
     * @OA\Delete(
     *     path="/tasks/{id}",
     *     summary="Exclui uma tarefa",
     *     description="Remove uma tarefa do sistema",
     *     tags={"Tasks"},
     *     security={{"apiKey": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tarefa",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tarefa excluída com sucesso"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado - API Key inválida"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tarefa não encontrada"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->taskService->deleteTask($id);
        
        if (!$deleted) {
            return response()->json([
                'error' => 'Tarefa não encontrada',
                'message' => 'A tarefa solicitada não existe no sistema'
            ], 404);
        }
        
        return response()->json([
            'message' => 'Tarefa excluída com sucesso'
        ]);
    }
}



