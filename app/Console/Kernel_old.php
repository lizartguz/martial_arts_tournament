<?php

namespace App\Console;

use App\Console\Commands\DailyDataCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */

    protected $commands = [
       
    ];
    /**
     * Registra tareas programadas de consola.
     */
    protected function schedule(Schedule $schedule): void
    {
       
    }

    /**
     * Carga los comandos disponibles para Artisan.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
