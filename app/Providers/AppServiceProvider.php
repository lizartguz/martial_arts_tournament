<?php

namespace App\Providers;

use App\AI\AIManager;
use App\AI\Contracts\AIProvider;
use App\Automation\AutomationRegistry;
use App\Services\Automation\AutomationExecutor;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AIManager::class, function () {
            return new AIManager(config('ai'));
        });

        $this->app->bind(AIProvider::class, function ($app) {
            return $app->make(AIManager::class)->driver();
        });

        $this->app->singleton(AutomationRegistry::class, function () {
            return AutomationRegistry::default();
        });

        $this->app->bind(AutomationExecutor::class, function ($app) {
            return new AutomationExecutor(
                provider: $app->make(AIProvider::class),
                automationRegistry: $app->make(AutomationRegistry::class),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
