<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * AppServiceProvider
 * 
 * Provider principal da aplicação.
 * Registra serviços globais e configurações da aplicação.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra serviços no container
     * 
     * @return void
     */
    public function register(): void
    {
        //
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



