<?php

namespace App\Http\Controllers;

use App\Contracts\Services\LogServiceInterface;
use App\Http\Resources\LogResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * LogController
 * 
 * Controller para gerenciamento de logs.
 * Implementa endpoints para consulta de logs de eventos.
 * 
 * Endpoints implementados:
 * - GET /logs: Lista logs recentes (últimos 30)
 * - GET /logs?id={id}: Busca log específico por ID
 */
class LogController extends Controller
{
    /**
     * Serviço de logs
     * 
     * @var LogServiceInterface
     */
    protected LogServiceInterface $logService;

    /**
     * Construtor
     * 
     * @param LogServiceInterface $logService
     */
    public function __construct(LogServiceInterface $logService)
    {
        $this->logService = $logService;
    }

    /**
     * Lista logs recentes ou busca log específico
     * 
     * @param Request $request
     * @return AnonymousResourceCollection|JsonResponse
     * 
     * @OA\Get(
     *     path="/logs",
     *     summary="Lista logs recentes",
     *     description="Retorna os últimos 30 logs da aplicação ou um log específico por ID",
     *     tags={"Logs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="ID do log específico (opcional)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Logs retornados com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Log"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Log não encontrado"
     *     )
     * )
     */
    public function index(Request $request)
    {
        // Verifica se foi solicitado um log específico
        if ($request->has('id') && !empty($request->get('id'))) {
            return $this->show($request->get('id'));
        }

        // Lista logs recentes
        $logs = $this->logService->listRecentLogs(30);
        
        return LogResource::collection($logs);
    }

    /**
     * Busca um log específico por ID
     * 
     * @param string $id
     * @return JsonResponse
     * 
     * @OA\Get(
     *     path="/logs/{id}",
     *     summary="Busca log por ID",
     *     description="Retorna os dados de um log específico",
     *     tags={"Logs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do log",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Log encontrado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Log")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Log não encontrado"
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        // Valida o ID
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|string|min:1'
        ]);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $log = $this->logService->getLogById($id);
        
        if (!$log) {
            return response()->json([
                'error' => 'Log não encontrado',
                'message' => 'O log solicitado não existe no sistema'
            ], 404);
        }
        
        return response()->json([
            'data' => new LogResource($log)
        ]);
    }
}



