<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * LogResource
 * 
 * Resource para formatação de dados de logs.
 * Implementa o padrão API Resource do Laravel para padronizar
 * as respostas da API e controlar quais dados são expostos.
 * 
 * Este resource garante que apenas os dados necessários sejam
 * retornados ao cliente, melhorando a performance e segurança.
 */
class LogResource extends JsonResource
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
            'id' => $this->_id,
            'entity_type' => $this->entity_type,
            'entity_id' => $this->entity_id,
            'action' => $this->action,
            'action_label' => $this->getActionLabel(),
            'data' => $this->data,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Obtém o label da ação em português
     * 
     * @return string
     */
    protected function getActionLabel(): string
    {
        return match ($this->action) {
            'created' => 'Criado',
            'updated' => 'Atualizado',
            'deleted' => 'Excluído',
            default => 'Desconhecido'
        };
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



