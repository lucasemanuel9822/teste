<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * ApiKeyMiddleware
 * 
 * Middleware para autenticação via API Key.
 * Implementa o primeiro pilar de segurança: Autenticação e Autorização.
 * 
 * Este middleware verifica se a requisição contém uma API Key válida
 * no cabeçalho X-API-KEY, protegendo endpoints que modificam dados.
 */
class ApiKeyMiddleware
{
    /**
     * Manipula uma requisição HTTP
     * 
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtém a API Key do cabeçalho da requisição
        $apiKey = $request->header('X-API-KEY');
        
        // Obtém a API Key válida do ambiente
        $validApiKey = env('API_KEY');

        // Verifica se a API Key foi fornecida
        if (!$apiKey) {
            return response()->json([
                'error' => 'API Key não fornecida',
                'message' => 'O cabeçalho X-API-KEY é obrigatório para esta operação',
                'code' => 'MISSING_API_KEY'
            ], 401);
        }

        // Verifica se a API Key é válida
        if ($apiKey !== $validApiKey) {
            return response()->json([
                'error' => 'API Key inválida',
                'message' => 'A API Key fornecida não é válida',
                'code' => 'INVALID_API_KEY'
            ], 401);
        }

        // Adiciona informações de autenticação ao request
        $request->merge([
            'authenticated' => true,
            'api_key' => $apiKey
        ]);

        // Continua com a requisição
        return $next($request);
    }
}



