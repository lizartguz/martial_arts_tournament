<?php

namespace App\Services;

use App\Models\NotificationM;
use Google\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class PushNotificationService
{
    public function __construct(
        private readonly FcmTokenService $fcmTokenService,
    ) {}

    // Envía una notificación a los usuarios indicados usando el canal configurado.
    public function sendToUsers(?array $userIds, string $deliveryPlatform, string $title, string $body, array $options = []): array
    {
        $tokens = $this->fcmTokenService->getActiveTokensForUsers($userIds, $deliveryPlatform);

        return $this->sendToTokenRecords($tokens, $title, $body, $options);
    }

    // Envía una notificación push usando la configuración guardada en un aviso de marketing.
    public function sendNotificationRecord(NotificationM $notification): array
    {
        $metadata = is_array($notification->metadata) ? $notification->metadata : [];
        $targetUserIds = collect($metadata['target_user_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values()
            ->all();
        $detailUrl = route('notifications.show', $notification);

        $result = $this->sendToUsers(
            empty($targetUserIds) ? null : $targetUserIds,
            $notification->delivery_platform ?? 'all',
            (string) $notification->title,
            (string) $notification->description,
            [
                'link' => $detailUrl,
                'data' => [
                    'notification_id' => (string) $notification->id,
                    'delivery_platform' => (string) ($notification->delivery_platform ?? 'all'),
                    'link' => $detailUrl,
                    'related_link' => (string) ($notification->link ?? ''),
                ],
            ]
        );

        if ($result['success_count'] > 0) {
            $notification->forceFill([
                'push_sent_at' => Carbon::now('America/La_Paz'),
                'push_last_error_at' => null,
                'push_last_error_message' => null,
            ])->save();
        } elseif ($result['success_count'] === 0 && $result['failure_count'] === 0) {
            $notification->forceFill([
                'push_last_error_at' => Carbon::now('America/La_Paz'),
                'push_last_error_message' => 'No existen tokens activos para el destino configurado.',
            ])->save();
        } elseif ($result['failure_count'] > 0) {
            $notification->forceFill([
                'push_last_error_at' => Carbon::now('America/La_Paz'),
                'push_last_error_message' => $result['errors'][0] ?? 'Error desconocido al enviar push',
            ])->save();
        }

        return $result;
    }

    // Envía los avisos activos cuya programación ya venció y que aún no se dispararon por push.
    public function dispatchPendingNotifications(?int $limit = null): array
    {
        $summary = [
            'processed' => 0,
            'sent' => 0,
            'failed' => 0,
            'notification_ids' => [],
        ];

        $now = Carbon::now('America/La_Paz');

        $query = NotificationM::query()
            ->where('state', 1)
            ->whereNull('push_sent_at')
            ->whereNull('push_last_error_at')
            ->whereDate('deadline', '>=', $now->toDateString())
            ->where(function ($builder) use ($now) {
                $builder->whereNull('scheduled_at')
                    ->orWhere('scheduled_at', '<=', $now);
            })
            ->orderBy('scheduled_at')
            ->orderBy('id');

        if ($limit !== null) {
            $query->limit($limit);
        }

        $query->get()->each(function (NotificationM $notification) use (&$summary) {
            $summary['processed']++;
            $summary['notification_ids'][] = $notification->id;

            $result = $this->sendNotificationRecord($notification);

            if (($result['success_count'] ?? 0) > 0) {
                $summary['sent']++;
            } elseif (($result['failure_count'] ?? 0) > 0) {
                $summary['failed']++;
            }
        });

        return $summary;
    }

    // Envía a una colección de tokens FCM y procesa errores conocidos de Firebase.
    public function sendToTokenRecords(Collection $tokens, string $title, string $body, array $options = []): array
    {
        $summary = [
            'success_count' => 0,
            'failure_count' => 0,
            'errors' => [],
        ];

        if ($tokens->isEmpty()) {
            return $summary;
        }

        $accessToken = $this->getAccessToken();
        $url = config('services.firebase.petition');

        foreach ($tokens as $tokenRecord) {
            $payload = $this->buildPayload(
                $tokenRecord->token,
                $title,
                $body,
                $options['link'] ?? null,
                $options['image'] ?? null,
                $options['data'] ?? []
            );

            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->post($url, $payload);

            if ($response->successful()) {
                $summary['success_count']++;
                $this->fcmTokenService->markTokenAsSent($tokenRecord);

                continue;
            }

            $summary['failure_count']++;
            $errorMessage = $this->extractFirebaseErrorMessage($response->json());
            $summary['errors'][] = $errorMessage;

            if ($this->shouldInvalidateToken($response->json())) {
                $this->fcmTokenService->invalidateToken($tokenRecord, $errorMessage);
            } else {
                $this->fcmTokenService->markTokenWithError($tokenRecord, $errorMessage);
            }

            Log::warning('Error enviando push con Firebase.', [
                'token_id' => $tokenRecord->id,
                'user_id' => $tokenRecord->user_id,
                'response_status' => $response->status(),
                'response_body' => $response->json(),
            ]);
        }

        return $summary;
    }

    // Obtiene y cachea el access token OAuth necesario para usar FCM HTTP v1.
    private function getAccessToken(): string
    {
        return Cache::remember('firebase.messaging.access_token', now()->addMinutes(45), function () {
            $serviceAccountFile = $this->resolveServiceAccountFile();

            try {
                $client = new Client;
                $client->setAuthConfig($serviceAccountFile);
                $client->addScope(config('services.firebase.scope'));

                $tokenData = $client->fetchAccessTokenWithAssertion();

                if (! array_key_exists('access_token', $tokenData)) {
                    throw new RuntimeException('Firebase no devolvió un access token.');
                }

                return $tokenData['access_token'];
            } catch (Throwable $exception) {
                Log::error('No se pudo autenticar Firebase Cloud Messaging.', [
                    'exception' => $exception::class,
                    'error' => $exception->getMessage(),
                ]);

                throw new RuntimeException(
                    'Firebase Cloud Messaging no pudo autenticarse con las credenciales configuradas.',
                    previous: $exception
                );
            }
        });
    }

    // Resuelve y valida la credencial privada sin exponer rutas internas al usuario.
    private function resolveServiceAccountFile(): string
    {
        $configuredPath = trim((string) config('services.firebase.sdk'));

        if ($configuredPath === '') {
            throw new RuntimeException(
                'Firebase Push no está configurado en el servidor. Verifique la variable FIREBASE_SDK.'
            );
        }

        $serviceAccountFile = $this->isAbsolutePath($configuredPath)
            ? $configuredPath
            : storage_path($configuredPath);

        if (! is_file($serviceAccountFile) || ! is_readable($serviceAccountFile)) {
            Log::error('No se pudo acceder al archivo de credenciales de Firebase.', [
                'configured_path' => $configuredPath,
                'resolved_path' => $serviceAccountFile,
                'file_exists' => is_file($serviceAccountFile),
                'file_readable' => is_readable($serviceAccountFile),
            ]);

            throw new RuntimeException(
                'No se pudo acceder a las credenciales privadas de Firebase en el servidor.'
            );
        }

        return $serviceAccountFile;
    }

    // Detecta rutas absolutas de Linux, Windows y recursos compartidos de red.
    private function isAbsolutePath(string $path): bool
    {
        return str_starts_with($path, '/')
            || str_starts_with($path, '\\\\')
            || preg_match('/^[A-Za-z]:[\\\\\/]/', $path) === 1;
    }

    // Construye el payload común para Android y Web Push.
    private function buildPayload(string $token, string $title, string $body, ?string $link, ?string $image, array $data): array
    {
        $stringData = collect($data)
            ->mapWithKeys(fn ($value, $key) => [$key => (string) $value])
            ->all();

        return [
            'message' => [
                'token' => $token,
                'notification' => array_filter([
                    'title' => $title,
                    'body' => $body,
                    'image' => $image,
                ]),
                'data' => $stringData,
                'webpush' => [
                    'notification' => array_filter([
                        'title' => $title,
                        'body' => $body,
                        'icon' => asset('frontend/images/logo_with_text.png'),
                        'image' => $image,
                    ]),
                    'fcm_options' => array_filter([
                        'link' => $link,
                    ]),
                ],
                'android' => [
                    'priority' => 'HIGH',
                    'notification' => array_filter([
                        'click_action' => $link,
                        'image' => $image,
                    ]),
                ],
            ],
        ];
    }

    // Extrae un mensaje legible desde la respuesta de error de Firebase.
    private function extractFirebaseErrorMessage(?array $response): string
    {
        return $response['error']['message']
            ?? $response['error']['status']
            ?? 'Firebase devolvió un error desconocido';
    }

    // Determina si el token debe invalidarse según la respuesta de Firebase.
    private function shouldInvalidateToken(?array $response): bool
    {
        $errorCode = $response['error']['details'][0]['errorCode'] ?? null;
        $status = $response['error']['status'] ?? null;

        return in_array($errorCode, ['UNREGISTERED', 'INVALID_ARGUMENT'], true)
            || in_array($status, ['NOT_FOUND', 'INVALID_ARGUMENT'], true);
    }
}
