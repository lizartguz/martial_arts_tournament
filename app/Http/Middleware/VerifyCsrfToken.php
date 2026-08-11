<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyCsrfToken
{
    /**
     * Las URIs que deben estar excluidas de la verificación CSRF.
     *
     * @var array<int, string>
     */
    protected $except = [
        'outofservice/webhook', // Excluir esta ruta
    ];
    
    
    /**
     * Ejecuta la operación principal del comando.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
