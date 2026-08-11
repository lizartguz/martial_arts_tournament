<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
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

                $this->ensureUserCanAuthenticate($user);

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
     * Busca los datos base del usuario para validar credenciales.
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
            )
            ->first();
    }

    /**
     * Valida que el usuario este activo y tenga alguno de los roles definidos por el sistema.
     */
    private function ensureUserCanAuthenticate(User $user): void
    {
        if (! $this->isEnabledUser($user)) {
            $this->denyLogin('Tu cuenta no está activa.');
        }

        if (! $user->hasAnyRole(array_keys((array) config('authorization.role_ranks', [])))) {
            $this->denyLogin('No tienes los permisos necesarios para acceder.');
        }
    }

    /**
     * Verifica que la cuenta no este desactivada manualmente.
     */
    private function isEnabledUser(User $user): bool
    {
        return (int) $user->state === 1;
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
