<?php

namespace App\Support;

/**
 * Nonce de Content-Security-Policy memoizado por request.
 *
 * Se registra como singleton (ver AppServiceProvider) para que valga lo
 * mismo sin importar quién lo pida primero: el middleware que arma la
 * cabecera, o una vista que imprime <script nonce="...">. Si nadie lo pide,
 * no se genera nada; si lo piden dos veces, la segunda reutiliza el mismo
 * valor en vez de generar uno distinto.
 */
class CspNonce
{
    private ?string $value = null;

    /**
     * Valor del nonce para el request actual, generándolo la primera vez.
     */
    public static function value(): string
    {
        return app(static::class)->resolve();
    }

    private function resolve(): string
    {
        return $this->value ??= base64_encode(random_bytes(16));
    }
}
