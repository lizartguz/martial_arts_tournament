<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthorizationAuditLogger
{
    /**
     * Ejecuta la operación record del servicio.
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
     * Ejecuta la operación unauthorized del servicio.
     */
    public function unauthorized(string $action, array $context = []): void
    {
        $this->record("Intento no autorizado: {$action}", $context, 'warning');
    }

    /**
     * Ejecuta la operación user context del servicio.
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
