<?php

namespace App\Providers;

use App\Services\SystemSettingsService;
use App\Support\CspNonce;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra servicios compartidos en el contenedor.
     */
    public function register(): void
    {
        $this->app->singleton(SystemSettingsService::class);
        // Singleton, no por request: aunque un test/comando resuelva el
        // contenedor varias veces sin pasar por el middleware, el nonce
        // generado la primera vez es el mismo que ve cualquier otro llamador.
        $this->app->singleton(CspNonce::class);
    }

    /**
     * Ejecuta configuraciones de arranque de la aplicación.
     */
    public function boot(): void
    {
        //
    }
}
