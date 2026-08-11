<?php

namespace App\Providers;

use App\Services\SystemSettingsService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra servicios compartidos en el contenedor.
     */
    public function register(): void
    {
        $this->app->singleton(SystemSettingsService::class);
    }

    /**
     * Ejecuta configuraciones de arranque de la aplicación.
     */
    public function boot(): void
    {
        //
    }
}
