<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Registra servicios compartidos en el contenedor.
     */
    public function register(): void
    {
        /**
         * Registra el manejo de reporte para esta excepcion.
         */
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
