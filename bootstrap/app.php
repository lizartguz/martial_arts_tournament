<?php

use App\Http\Middleware\ForceJsonResponse;
use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Livewire\Mechanisms\HandleComponents\CorruptComponentPayloadException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            LocaleMiddleware::class,
        ]);
        $middleware->api(prepend: [
            ForceJsonResponse::class,
        ]);
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (CorruptComponentPayloadException $exception) {
            $request = app(Request::class);
            $snapshot = data_get($request->input('components'), '0.snapshot');
            $snapshotData = is_string($snapshot) ? json_decode($snapshot, true) : [];

            Log::warning('[Livewire] Corrupt component payload detected', [
                'message' => $exception->getMessage(),
                'component_name' => data_get($snapshotData, 'memo.name'),
                'component_id' => data_get($snapshotData, 'memo.id'),
                'path' => $request->path(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'forwarded_for' => $request->header('x-forwarded-for'),
                'user_agent' => $request->userAgent(),
                'referer' => $request->headers->get('referer'),
                'accept_language' => $request->headers->get('accept-language'),
            ]);
        })->stop();
    })->create();
