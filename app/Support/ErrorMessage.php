<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Throwable;

class ErrorMessage
{
    /**
     * Mensaje de error a mostrar al usuario del panel.
     * Los roles en config('authorization.error_detail_roles') ven el detalle
     * tecnico crudo (util para depurar). Cualquier otro rol ve el mensaje
     * amigable provisto, o uno generico por defecto.
     */
    public static function forUser(Throwable|string $error, ?string $friendly = null): string
    {
        if (self::userSeesTechnicalDetail()) {
            return $error instanceof Throwable ? $error->getMessage() : $error;
        }

        return $friendly ?? __('messages.errors.unexpected');
    }

    /** Indica si el usuario autenticado puede ver el detalle tecnico crudo. */
    public static function userSeesTechnicalDetail(): bool
    {
        $user = Auth::user();

        return $user !== null
            && $user->hasAnyRole((array) config('authorization.error_detail_roles', []));
    }
}
