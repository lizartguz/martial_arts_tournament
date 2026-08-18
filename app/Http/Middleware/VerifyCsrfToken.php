<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * URIs excluidas de la verificación CSRF cuando este middleware legado se registre.
     *
     * @var array<int, string>
     */
    protected $except = [
        'outofservice/webhook',
    ];
}
