<?php

namespace App\Http\Middleware;

use App\Support\CspNonce;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
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
        // CspNonce::value() memoiza en un singleton: cualquier vista que lo
        // pida durante el render (los ~11 <script> inline) obtiene el mismo
        // valor que se usa acá abajo para la cabecera, sin depender del orden
        // de ejecución. Necesario porque no todo render pasa por este
        // middleware (Livewire::test() en los tests invoca el componente
        // directo, sin pasar por el pipeline HTTP).
        $nonce = CspNonce::value();

        // @vite() inyecta un <script> de precarga de módulos que no aparece
        // en ningún grep sobre las vistas (lo genera Illuminate\Foundation\Vite
        // en runtime, no es texto Blade). useCspNonce() hace que ese script y
        // los tags de @vite() en sí mismos lleven el nonce automáticamente.
        Vite::useCspNonce($nonce);

        // Debugbar (solo activo en local, ver config/debugbar.php) hace mucho
        // más que los <script> que arma su propio renderer: internamente
        // inyecta contenido dinámico por widget que setCspNonce() no cubre.
        // Apenas hay UN nonce presente en la directiva, los navegadores
        // modernos ignoran 'unsafe-inline' para todo lo no-nonceado de esa
        // respuesta, ese contenido interno incluido. Mientras esté activo
        // (nunca en producción) esta respuesta se sirve sin nonce en vez de
        // que la barra quede rota a medias.
        $debugbarActive = app()->bound('debugbar') && app('debugbar')->isEnabled();

        if ($debugbarActive) {
            app('debugbar')->getJavascriptRenderer()->setCspNonce($nonce);
        }

        $response = $next($request);

        $this->setIfMissing($response, 'X-Content-Type-Options', 'nosniff');
        $this->setIfMissing($response, 'X-Frame-Options', 'SAMEORIGIN');
        $this->setIfMissing($response, 'Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->setIfMissing($response, 'Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $this->setIfMissing($response, 'Content-Security-Policy', implode('; ', [
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            // 'unsafe-eval' porque Alpine.js (empaquetado con Livewire) evalúa
            // sus expresiones (x-data, x-show, @click...) con Function().
            // 'nonce-...' cambia por request; solo los <script> que lo llevan
            // (los ~11 inline legítimos) se ejecutan. 'unsafe-inline' queda de
            // respaldo para navegadores viejos que no entienden nonces: los que
            // sí lo entienden lo ignoran en cuanto ven el nonce presente, así
            // que no reabre nada — salvo con Debugbar activo, ver arriba.
            // googletagmanager.com porque el SDK de Firebase Analytics
            // (resources/js/analytics.js -> getAnalytics()) inyecta gtag.js
            // ahí en tiempo de ejecución; no aparece en ningún grep sobre las
            // vistas porque no es un <script src> en el HTML.
            $debugbarActive
                ? "script-src 'self' 'unsafe-eval' 'unsafe-inline' https://www.googletagmanager.com"
                : "script-src 'self' 'unsafe-eval' 'unsafe-inline' 'nonce-{$nonce}' https://www.googletagmanager.com",
            // Cubre los 4 onclick=/onerror= que quedan sin convertir: al ser
            // una directiva aparte de script-src, el nonce de arriba no la
            // afecta. Dejarlos así es más seguro que reescribirlos a ciegas a
            // @click de Alpine sin poder probar cada uno en el navegador.
            "script-src-attr 'unsafe-inline'",
            // Los ~54 atributos style="" inline repartidos en el proyecto
            // necesitan 'unsafe-inline'; una inyección de CSS es mucho menos
            // grave que una de JS, no vale la pena migrarlos hoy para esto.
            // fonts.bunny.net porque las vistas de auth (guest.blade.php) y
            // welcome_copy.blade.php cargan la tipografía Figtree desde ahí.
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net",
        ]));

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
