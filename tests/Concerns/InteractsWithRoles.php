<?php

namespace Tests\Concerns;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Laravel\Jetstream\Http\Middleware\AuthenticateSession;
use Spatie\Permission\PermissionRegistrar;

trait InteractsWithRoles
{
    protected function seedRoles(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    protected function userWithRole(string $role, array $overrides = []): User
    {
        $user = User::factory()->create($overrides);
        $user->assignRole($role);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $user;
    }

    protected function actingAsRole(string $role, array $overrides = []): User
    {
        $user = $this->userWithRole($role, $overrides);

        $this->withoutMiddleware(AuthenticateSession::class);
        $this->actingAs($user);

        return $user;
    }
}
