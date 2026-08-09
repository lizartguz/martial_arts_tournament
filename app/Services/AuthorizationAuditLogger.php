<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthorizationAuditLogger
{
    /**
     * Registra una accion de autorizacion con usuario, IP y contexto.
     */
    public function record(string $event, array $context = [], string $level = 'info'): void
    {
        $actor = auth()->user();

        Log::channel('stack')->{$level}("[Authorization] {$event}", array_merge([
            'actor' => $this->userContext($actor),
            'ip' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'occurred_at' => now()->format('Y-m-d H:i:s'),
        ], $context));
    }

    /**
     * Registra un intento bloqueado por falta de permisos o jerarquia.
     */
    public function unauthorized(string $action, array $context = []): void
    {
        $this->record("Intento no autorizado: {$action}", $context, 'warning');
    }

    /**
     * Resume los datos del usuario para dejar trazabilidad en logs.
     */
    public function userContext(?User $user): ?array
    {
        if (! $user) {
            return null;
        }

        $user->loadMissing('roles');

        return [
            'id' => $user->id,
            'name' => trim((string) $user->name . ' ' . (string) $user->lastname),
            'email' => $user->email,
            'roles' => $user->roles->pluck('name')->values()->all(),
        ];
    }
}
