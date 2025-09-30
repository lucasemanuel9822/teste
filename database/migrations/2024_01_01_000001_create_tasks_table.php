<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration para criação da tabela tasks
 * 
 * Cria a tabela principal para armazenar as tarefas
 * com índices otimizados para consultas por status.
 */
return new class extends Migration
{
    /**
     * Executa a migração
     * 
     * @return void
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            // Chave primária auto-incremento
            $table->id();
            
            // Campos obrigatórios da tarefa
            $table->string('title', 255)->comment('Título da tarefa');
            $table->text('description')->nullable()->comment('Descrição detalhada da tarefa');
            
            // Status da tarefa com valor padrão
            $table->enum('status', ['pending', 'in_progress', 'completed'])
                  ->default('pending')
                  ->comment('Status atual da tarefa');
            
            // Timestamps automáticos
            $table->timestamps();
            
            // Índices para otimização de consultas
            $table->index('status', 'idx_tasks_status'); // Para filtros por status
            $table->index('created_at', 'idx_tasks_created_at'); // Para ordenação por data
            $table->index(['status', 'created_at'], 'idx_tasks_status_created'); // Índice composto
        });
    }

    /**
     * Reverte a migração
     * 
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

