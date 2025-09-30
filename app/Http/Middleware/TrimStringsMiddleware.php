<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * TrimStringsMiddleware
 * 
 * Middleware para limpeza e higienização de dados.
 * Implementa o terceiro pilar de segurança: Proteção e Higienização de Dados.
 * 
 * Este middleware remove espaços em branco desnecessários e converte
 * strings vazias para null, melhorando a qualidade dos dados.
 */
class TrimStringsMiddleware
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
        // Processa dados da requisição
        $this->cleanRequestData($request);
        
        // Continua com a requisição
        return $next($request);
    }

    /**
     * Limpa os dados da requisição
     * 
     * @param Request $request
     * @return void
     */
    protected function cleanRequestData(Request $request): void
    {
        // Limpa dados do body (JSON, form data, etc.)
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH')) {
            $data = $request->all();
            $cleanedData = $this->trimArray($data);
            $request->merge($cleanedData);
        }

        // Limpa parâmetros de query
        $queryParams = $request->query();
        $cleanedQuery = $this->trimArray($queryParams);
        
        // Substitui os parâmetros de query
        $request->query->replace($cleanedQuery);
    }

    /**
     * Aplica trim recursivamente em um array
     * 
     * @param array $data
     * @return array
     */
    protected function trimArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                // Remove espaços em branco
                $trimmed = trim($value);
                
                // Converte string vazia para null
                $data[$key] = $trimmed === '' ? null : $trimmed;
            } elseif (is_array($value)) {
                // Processa arrays recursivamente
                $data[$key] = $this->trimArray($value);
            }
        }

        return $data;
    }
}



