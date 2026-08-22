<?php

namespace App\Services;

use App\Models\FcmToken;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FcmTokenService
{
    private const DEVICE_IDENTIFIER_MAX_LENGTH = 300;

    private const DEVICE_NAME_MAX_LENGTH = 150;

    private const BROWSER_MAX_LENGTH = 120;

    // Registra o actualiza un token móvil para el usuario indicado.
    /**
     * Registra mobile token for user para el usuario.
     */
    public function registerMobileTokenForUser(int $userId, string $token, array $context = []): FcmToken
    {
        return $this->registerTokenForUser($userId, $token, [
            'platform' => 'mobile',
            'delivery_platform' => 'android',
            'device_identifier' => $context['device_identifier'] ?? null,
            'device_name' => $context['device_name'] ?? 'Dispositivo móvil',
            'browser' => null,
        ]);
    }

    // Registra o actualiza un token de Web Push para el usuario autenticado.
    /**
     * Registra web token for user para el usuario.
     */
    public function registerWebTokenForUser(User $user, string $token, array $context = []): FcmToken
    {
        return $this->registerTokenForUser($user->id, $token, [
            'platform' => 'web',
            'delivery_platform' => 'web',
            'device_identifier' => $context['device_identifier'] ?? null,
            'device_name' => $context['device_name'] ?? 'Navegador web',
            'browser' => $context['browser'] ?? null,
        ]);
    }

    // Crea o reactiva un token FCM, reasignándolo al usuario actual si cambió de cuenta.
    /**
     * Registra token for user para el usuario.
     */
    public function registerTokenForUser(int $userId, string $token, array $attributes): FcmToken
    {
        $now = Carbon::now('America/La_Paz');

        return FcmToken::query()->updateOrCreate(
            ['token' => $token],
            [
                'user_id' => $userId,
                'platform' => $attributes['platform'],
                'delivery_platform' => $attributes['delivery_platform'],
                'device_identifier' => $this->limitNullableString(
                    $attributes['device_identifier'] ?? null,
                    self::DEVICE_IDENTIFIER_MAX_LENGTH
                ),
                'device_name' => $this->limitNullableString(
                    $attributes['device_name'] ?? null,
                    self::DEVICE_NAME_MAX_LENGTH
                ),
                'browser' => $this->limitNullableString(
                    $attributes['browser'] ?? null,
                    self::BROWSER_MAX_LENGTH
                ),
                'last_seen_at' => $now,
                'invalidated_at' => null,
                'last_error_at' => null,
                'last_error_message' => null,
                'is_active' => true,
            ]
        );
    }

    // Comprueba si un token web sigue activo y pertenece al usuario autenticado.
    /**
     * Indica si active web token for user.
     */
    public function isActiveWebTokenForUser(User $user, string $token): bool
    {
        return FcmToken::query()
            ->where('token', $token)
            ->where('user_id', $user->id)
            ->where('platform', 'web')
            ->where('delivery_platform', 'web')
            ->where('is_active', true)
            ->whereNull('invalidated_at')
            ->exists();
    }

    // Ajusta metadatos descriptivos al tamaño real de sus columnas sin modificar el token FCM.
    /**
     * Ejecuta la operación limit nullable string del servicio.
     */
    private function limitNullableString(mixed $value, int $maxLength): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        return Str::limit(trim((string) $value), $maxLength, '');
    }

    // Devuelve los tokens activos filtrados por usuarios y canal de entrega.
    /**
     * Devuelve active tokens for users solicitado.
     */
    public function getActiveTokensForUsers(?array $userIds, string $deliveryPlatform): Collection
    {
        return FcmToken::query()
            /**
             * Aplica el filtro solo cuando existe un criterio activo.
             */
            ->when(! empty($userIds), function ($query) use ($userIds) {
                $query->whereIn('user_id', $userIds);
            })
            ->where('is_active', true)
            ->whereNull('invalidated_at')
            /**
             * Aplica el filtro solo cuando existe un criterio activo.
             */
            ->when($deliveryPlatform !== 'all', function ($query) use ($deliveryPlatform) {
                $query->where('platform', $deliveryPlatform);
            })
            ->get();
    }

    // Marca un token como invalido cuando Firebase reporta que ya no se puede usar.
    /**
     * Invalida token by value cuando deja de ser usable.
     */
    public function invalidateTokenByValue(string $token, ?string $message = null): void
    {
        $record = FcmToken::query()->where('token', $token)->first();

        if (! $record) {
            return;
        }

        $this->invalidateToken($record, $message);
    }

    // Desactiva un token sin tratarlo como error de Firebase, por ejemplo cuando el navegador se desuscribe.
    /**
     * Desactiva token by value solicitado.
     */
    public function deactivateTokenByValue(string $token, ?string $message = null): void
    {
        $record = FcmToken::query()->where('token', $token)->first();

        if (! $record) {
            return;
        }

        $record->forceFill([
            'is_active' => false,
            'invalidated_at' => Carbon::now('America/La_Paz'),
            'last_error_at' => null,
            'last_error_message' => $message,
        ])->save();
    }

    /**
     * Desactiva solo tokens web que pertenecen al usuario autenticado.
     */
    public function deactivateWebTokenForUser(User $user, string $token, ?string $message = null): void
    {
        $record = FcmToken::query()
            ->where('token', $token)
            ->where('user_id', $user->id)
            ->where('platform', 'web')
            ->where('delivery_platform', 'web')
            ->first();

        if (! $record) {
            return;
        }

        $record->forceFill([
            'is_active' => false,
            'invalidated_at' => Carbon::now('America/La_Paz'),
            'last_error_at' => null,
            'last_error_message' => $message,
        ])->save();
    }

    // Marca un token como invalido sin borrarlo para poder auditar el error.
    /**
     * Invalida token cuando deja de ser usable.
     */
    public function invalidateToken(FcmToken $token, ?string $message = null): void
    {
        $token->forceFill([
            'is_active' => false,
            'invalidated_at' => Carbon::now('America/La_Paz'),
            'last_error_at' => Carbon::now('America/La_Paz'),
            'last_error_message' => $message,
        ])->save();
    }

    // Registra que un token fue usado en un envío exitoso.
    /**
     * Marca token as sent en el registro.
     */
    public function markTokenAsSent(FcmToken $token): void
    {
        $token->forceFill([
            'last_sent_at' => Carbon::now('America/La_Paz'),
            'last_error_at' => null,
            'last_error_message' => null,
        ])->save();
    }

    // Registra un error temporal sin invalidar automáticamente el token.
    /**
     * Marca token with error en el registro.
     */
    public function markTokenWithError(FcmToken $token, string $message): void
    {
        $token->forceFill([
            'last_error_at' => Carbon::now('America/La_Paz'),
            'last_error_message' => $message,
        ])->save();
    }
}
