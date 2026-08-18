<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Aplica cabeceras defensivas sin romper scripts y estilos existentes.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $this->setIfMissing($response, 'X-Content-Type-Options', 'nosniff');
        $this->setIfMissing($response, 'X-Frame-Options', 'SAMEORIGIN');
        $this->setIfMissing($response, 'Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->setIfMissing($response, 'Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $this->setIfMissing($response, 'Content-Security-Policy', "frame-ancestors 'self'; base-uri 'self'; object-src 'none'");

        if ($request->isSecure()) {
            $this->setIfMissing($response, 'Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    /**
     * Respeta cabeceras definidas por respuestas especializadas.
     */
    private function setIfMissing(Response $response, string $header, string $value): void
    {
        if (! $response->headers->has($header)) {
            $response->headers->set($header, $value);
        }
    }
}
