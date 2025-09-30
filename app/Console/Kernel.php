<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

/**
 * Console Kernel
 * 
 * Kernel para comandos Artisan no Lumen.
 * Necessário para o funcionamento do queue worker.
 */
class Kernel extends ConsoleKernel
{
    /**
     * Define os comandos da aplicação
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define o agendamento de comandos
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Aqui podem ser definidos comandos agendados se necessário
    }

    /**
     * Registra os comandos da aplicação
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
