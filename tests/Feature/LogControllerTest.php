<?php

namespace Tests\Feature;

use App\Models\Log;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * LogControllerTest
 * 
 * Testes de integração para o LogController.
 * Testa todos os endpoints relacionados a logs.
 */
class LogControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa listagem de logs recentes
     * 
     * @return void
     */
    public function test_can_list_recent_logs(): void
    {
        // Cria logs de teste
        Log::factory()->create(['action' => 'created']);
        Log::factory()->create(['action' => 'updated']);
        Log::factory()->create(['action' => 'deleted']);

        // Faz requisição
        $response = $this->get('/logs');

        // Verifica resposta
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'entity_type',
                    'entity_id',
                    'action',
                    'action_label',
                    'data',
                    'created_at',
                    'updated_at'
                ]
            ]
        ]);
    }

    /**
     * Testa busca de log específico por ID
     * 
     * @return void
     */
    public function test_can_get_log_by_id(): void
    {
        // Cria log de teste
        $log = Log::factory()->create();

        // Faz requisição
        $response = $this->get("/logs?id={$log->_id}");

        // Verifica resposta
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'entity_type',
                'entity_id',
                'action',
                'action_label',
                'data',
                'created_at',
                'updated_at'
            ]
        ]);
        $response->assertJsonPath('data.id', $log->_id);
    }

    /**
     * Testa busca de log inexistente
     * 
     * @return void
     */
    public function test_cannot_get_nonexistent_log(): void
    {
        // Faz requisição para ID inexistente
        $response = $this->get('/logs?id=507f1f77bcf86cd799439011');

        // Verifica resposta
        $response->assertStatus(404);
        $response->assertJsonStructure([
            'error',
            'message'
        ]);
    }

    /**
     * Testa busca de log com ID inválido
     * 
     * @return void
     */
    public function test_cannot_get_log_with_invalid_id(): void
    {
        // Faz requisição com ID inválido
        $response = $this->get('/logs?id=invalid-id');

        // Verifica resposta
        $response->assertStatus(404);
        $response->assertJsonStructure([
            'error',
            'message'
        ]);
    }
}



