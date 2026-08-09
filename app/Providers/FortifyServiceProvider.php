<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use App\Models\UserDependencyM;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    private const INTERNAL_ACCESS_TYPE = 0;
    private const SUBSCRIBER_ACCESS_TYPE = 1;
    private const INTERNAL_ROLE_NAMES = [
        'super_manager',
        'admin',
        'manager',
        'meteorology',
        'meteorologist',
        'technical',
        'sales',
        'marketing',
    ];
    private const EXTERNAL_ROLE_NAME = 'subscriber';

    /**
     * Registra servicios de la aplicacion.
     */
    public function register(): void
    {
        //
    }

    /**
     * Configura vistas, autenticacion y limites de Fortify.
     */
    public function boot(): void
    {
        app()->setLocale('es');

        Fortify::authenticateUsing(function (Request $request) {
            try {
                date_default_timezone_set('America/La_Paz');

                $request->validate([
                    'email' => ['required', 'email'],
                    'password' => ['required'],
                ]);

                $user = $this->findUserForLogin($request->input('email'));

                if (! $user || ! Hash::check($request->password, $user->password)) {
                    $this->denyLogin(__('auth.failed'));
                }

                $this->ensureUserCanAuthenticate($user, date('Y-m-d'));

                return $this->logAuthenticationOutcome($request, $user);
            } catch (ValidationException $exception) {
                $this->logFailedLoginAttempt($request, $exception);
                throw $exception;
            }
        });

        Fortify::loginView(function () {
            $request = request();

            Log::channel('stack')->info('Auth page visited: login', [
                'route' => '/login',
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referer' => $request->headers->get('referer'),
            ]);

            return view('auth.login');
        });

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }

    /**
     * Busca los datos base del usuario para validar credenciales y tipo de acceso.
     */
    private function findUserForLogin(string $email): ?User
    {
        return User::query()
            ->where('email', $email)
            ->select(
                'id',
                'name',
                'lastname',
                'password',
                'email',
                'number_phone',
                'image',
                'device_identifier',
                'state',
                'access_type',
                'user_type',
                'is_update',
                'payment_state',
                'own_state',
            )
            ->first();
    }

    /**
     * Carga la suscripcion para externos: owner, subowner o dependiente.
     */
    private function loadExternalSubscription(User $user): void
    {
        $subscription = $this->findSubscriptionFromDependency($user)
            ?? $this->findDirectSubscription($user);

        if (! $subscription) {
            return;
        }

        $user->date_expiry = $subscription->date_expiry;
        $user->temporary_extension_end = $subscription->temporary_extension_end;
        $user->type_subscriptions = $subscription->type_subscriptions;
    }

    /**
     * Busca la suscripcion propia desde el registro directo del titular.
     */
    private function findDirectSubscription(User $user): ?object
    {
        return DB::table('users_subscriptions as us')
            ->join('subscriptions as s', 's.id', '=', 'us.subscription_id')
            ->where('us.user_id', $user->id)
            ->select('us.date_expiry', 'us.temporary_extension_end', 's.name as type_subscriptions')
            ->orderByRaw("GREATEST(COALESCE(us.temporary_extension_end, '1000-01-01'), COALESCE(us.date_expiry, '1000-01-01')) DESC")
            ->orderByDesc('us.id')
            ->first();
    }

    /**
     * Busca la suscripcion asociada al usuario externo desde user_dependency.
     */
    private function findSubscriptionFromDependency(User $user): ?object
    {
        return UserDependencyM::join('users_subscriptions as us', 'us.id', '=', 'user_dependency.user_subscription_id')
            ->join('subscriptions as s', 's.id', '=', 'us.subscription_id')
            ->where('user_dependency.dependent_user_id', $user->id)
            ->select('us.date_expiry', 'us.temporary_extension_end', 's.name as type_subscriptions')
            ->orderByRaw("GREATEST(COALESCE(us.temporary_extension_end, '1000-01-01'), COALESCE(us.date_expiry, '1000-01-01')) DESC")
            ->orderByDesc('us.id')
            ->first();
    }

    /**
     * Deriva la validacion segun usuario interno o cliente externo.
     */
    private function ensureUserCanAuthenticate(User $user, string $currentDate): void
    {
        if ((int) $user->access_type === self::INTERNAL_ACCESS_TYPE) {
            $this->ensureInternalUserCanAuthenticate($user);
            return;
        }

        if ((int) $user->access_type === self::SUBSCRIBER_ACCESS_TYPE) {
            $this->loadExternalSubscription($user);
            $this->ensureExternalUserCanAuthenticate($user, $currentDate);
            return;
        }

        $this->denyLogin('No tienes los permisos necesarios para acceder.');
    }

    /**
     * Valida que un usuario interno este habilitado y tenga rol interno.
     */
    private function ensureInternalUserCanAuthenticate(User $user): void
    {
        if (! $this->isEnabledUser($user)) {
            $this->denyLogin('Tu cuenta no está activa.');
        }

        if (! $user->hasAnyRole(self::INTERNAL_ROLE_NAMES)) {
            $this->denyLogin('No tienes los permisos necesarios para acceder.');
        }
    }

    /**
     * Valida cuenta, pago, vigencia y rol para clientes externos.
     */
    private function ensureExternalUserCanAuthenticate(User $user, string $currentDate): void
    {
        if (! $this->isEnabledUser($user)) {
            $this->denyLogin('Tu cuenta no está activa.');
        }

        if (! $this->isPaidUser($user)) {
            $this->denyLogin('Tu cuenta no está activa.');
        }

        if (! $this->hasValidSubscription($user, $currentDate)) {
            $this->denyLogin('Tu suscripción ha expirado.');
        }

        if ((int) $user->own_state !== 1) {
            $this->denyLogin('Tu suscripción ha expirado.');
        }

        if ($user->type_subscriptions === 'Personal Demo') {
            $this->denyLogin('No tienes los permisos necesarios para acceder.');
        }

        if (! $this->hasOnlySubscriberRole($user)) {
            $this->denyLogin('No tienes los permisos necesarios para acceder.');
        }
    }

    /**
     * Determina si la suscripcion o extension temporal sigue vigente.
     * Usa la fecha de vigencia efectiva (la mayor entre expiracion y
     * prorroga) para coincidir con el criterio de ordenamiento de la consulta.
     */
    private function hasValidSubscription(User $user, string $currentDate): bool
    {
        $effectiveEnd = $this->effectiveSubscriptionEnd($user);

        return $effectiveEnd !== null && $effectiveEnd >= $currentDate;
    }

    /**
     * Devuelve la fecha de vigencia efectiva: la mayor entre date_expiry y
     * temporary_extension_end, o null si ninguna esta definida.
     */
    private function effectiveSubscriptionEnd(User $user): ?string
    {
        $dates = array_filter([
            $user->date_expiry,
            $user->temporary_extension_end,
        ]);

        return empty($dates) ? null : max($dates);
    }

    /**
     * Verifica que la cuenta no este desactivada manualmente.
     */
    private function isEnabledUser(User $user): bool
    {
        return (int) $user->state === 1;
    }

    /**
     * Verifica que el cliente externo tenga pago habilitado.
     */
    private function isPaidUser(User $user): bool
    {
        return (int) $user->payment_state === 1;
    }

    /**
     * Valida que el cliente externo tenga solamente el rol subscriber.
     */
    private function hasOnlySubscriberRole(User $user): bool
    {
        $user->loadMissing('roles');
        $roleNames = $user->roles->pluck('name');

        return $roleNames->count() === 1 && $roleNames->contains(self::EXTERNAL_ROLE_NAME);
    }

    /**
     * Lanza una respuesta de validacion fallida para el login.
     */
    private function denyLogin(string $message): void
    {
        throw ValidationException::withMessages([
            'email' => $message,
        ]);
    }

    /**
     * Registra el resultado final del intento de login.
     */
    private function logAuthenticationOutcome(Request $request, ?User $user): ?User
    {
        if ($user !== null) {
            Log::channel('stack')->info('Auth login succeeded', [
                'route' => '/login',
                'method' => $request->method(),
                'ip' => $request->ip(),
                'email_submitted' => $request->input('email'),
                'user_id' => $user->id ?? null,
                'user_email' => $user->email ?? null,
                'user_agent' => $request->userAgent(),
                'referer' => $request->headers->get('referer'),
            ]);

            return $user;
        }

        Log::channel('stack')->warning('Auth login failed', [
            'route' => '/login',
            'method' => $request->method(),
            'ip' => $request->ip(),
            'email_submitted' => $request->input('email'),
            'reason' => __('auth.failed'),
            'user_agent' => $request->userAgent(),
            'referer' => $request->headers->get('referer'),
        ]);

        return $user;
    }

    /**
     * Registra un intento de login rechazado por validacion.
     */
    private function logFailedLoginAttempt(Request $request, ValidationException $exception): void
    {
        $errors = $exception->errors();
        $reason = $errors['email'][0] ?? $exception->getMessage();

        Log::channel('stack')->warning('Auth login failed', [
            'route' => '/login',
            'method' => $request->method(),
            'ip' => $request->ip(),
            'email_submitted' => $request->input('email'),
            'reason' => $reason,
            'user_agent' => $request->userAgent(),
            'referer' => $request->headers->get('referer'),
        ]);
    }
}
