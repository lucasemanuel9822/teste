<?php

namespace App\Providers;

use App\Contracts\Repositories\LogRepositoryInterface;
use App\Contracts\Repositories\TaskRepositoryInterface;
use App\Contracts\Services\LogServiceInterface;
use App\Contracts\Services\TaskServiceInterface;
use App\Repositories\LogRepository;
use App\Repositories\TaskRepository;
use App\Services\LogService;
use App\Services\TaskService;
use Illuminate\Support\ServiceProvider;

/**
 * RepositoryServiceProvider
 * 
 * Provider para registro de dependências no container de injeção.
 * Implementa o padrão de Injeção de Dependência (DIP) do SOLID.
 * 
 * Este provider registra as interfaces e suas implementações,
 * permitindo que o Laravel resolva automaticamente as dependências.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Registra serviços no container
     * 
     * @return void
     */
    public function register(): void
    {
        // Registra repositórios
        $this->app->bind(
            TaskRepositoryInterface::class,
            TaskRepository::class
        );

        // Registra LogRepository
        $this->app->bind(
            LogRepositoryInterface::class,
            LogRepository::class
        );

        // Registra serviços
        $this->app->bind(
            TaskServiceInterface::class,
            function ($app) {
                return new TaskService(
                    $app->make(TaskRepositoryInterface::class),
                    $app->make(LogServiceInterface::class) // LogService ativado para registrar logs das tarefas
                );
            }
        );

        // Registra LogService
        $this->app->bind(
            LogServiceInterface::class,
            LogService::class
        );
    }

    /**
     * Inicializa serviços
     * 
     * @return void
     */
    public function boot(): void
    {
        //
    }
}


