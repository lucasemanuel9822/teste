<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * TaskResource
 * 
 * Resource para formatação de dados de tarefas.
 * Implementa o padrão API Resource do Laravel para padronizar
 * as respostas da API e controlar quais dados são expostos.
 * 
 * Este resource garante que apenas os dados necessários sejam
 * retornados ao cliente, melhorando a performance e segurança.
 */
class TaskResource extends JsonResource
{
    /**
     * Transforma o resource em um array
     * 
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }


    /**
     * Adiciona dados adicionais ao resource
     * 
     * @param Request $request
     * @return array
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'version' => '1.0',
                'timestamp' => now()->toISOString(),
            ],
        ];
    }
}


