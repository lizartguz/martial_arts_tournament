<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;

class AuthorizationHierarchyService
{
    /**
     * Ejecuta la operación super roles del servicio.
     */
    public function superRoles(): array
    {
        return (array) config('authorization.super_roles', []);
    }

    /**
     * Ejecuta la operación protected permissions del servicio.
     */
    public function protectedPermissions(): array
    {
        return (array) config('authorization.protected_permissions', []);
    }

    /**
     * Ejecuta la operación role rank del servicio.
     */
    public function roleRank(string $roleName): int
    {
        return (int) data_get(config('authorization.role_ranks', []), $roleName, 0);
    }

    /**
     * Ejecuta la operación user rank del servicio.
     */
    public function userRank(?User $user): int
    {
        if (! $user) {
            return 0;
        }

        return $user->roles
            ->pluck('name')
            ->map(fn (string $roleName) => $this->roleRank($roleName))
            ->max() ?? 0;
    }

    /**
     * Indica si super user.
     */
    public function isSuperUser(?User $user): bool
    {
        return $user !== null && $user->hasAnyRole($this->superRoles());
    }

    /**
     * Indica si protected role name.
     */
    public function isProtectedRoleName(string $roleName): bool
    {
        return in_array($roleName, $this->superRoles(), true);
    }

    /**
     * Valida si el usuario puede view role name.
     */
    public function canViewRoleName(?User $actor, string $roleName): bool
    {
        if (! $actor) {
            return false;
        }

        if ($this->isSuperUser($actor)) {
            return true;
        }

        return ! $this->isProtectedRoleName($roleName)
            && $this->roleRank($roleName) <= $this->userRank($actor);
    }

    /**
     * Valida si el usuario puede manage role name.
     */
    public function canManageRoleName(?User $actor, string $roleName): bool
    {
        if (! $actor) {
            return false;
        }

        if ($this->isSuperUser($actor)) {
            return true;
        }

        return ! $this->isProtectedRoleName($roleName)
            && $this->roleRank($roleName) < $this->userRank($actor);
    }

    /**
     * Valida si el usuario puede manage permission name.
     */
    public function canManagePermissionName(?User $actor, string $permissionName): bool
    {
        if (! $actor) {
            return false;
        }

        if ($this->isSuperUser($actor)) {
            return true;
        }

        return ! in_array($permissionName, $this->protectedPermissions(), true);
    }

    /**
     * Valida si el usuario puede view user.
     */
    public function canViewUser(?User $actor, ?User $target): bool
    {
        if (! $actor || ! $target) {
            return false;
        }

        $target->loadMissing('roles');
        $actor->loadMissing('roles');

        if ($this->isSuperUser($actor)) {
            return true;
        }

        if ($this->isSuperUser($target)) {
            return false;
        }

        return $this->userRank($target) <= $this->userRank($actor);
    }

    /**
     * Valida si el usuario puede manage user.
     */
    public function canManageUser(?User $actor, ?User $target): bool
    {
        if (! $actor || ! $target) {
            return false;
        }

        $target->loadMissing('roles');
        $actor->loadMissing('roles');

        if ($this->isSuperUser($actor)) {
            return true;
        }

        if ($this->isSuperUser($target)) {
            return false;
        }

        return $this->userRank($target) < $this->userRank($actor);
    }

    /**
     * Ejecuta la operación hidden role names for del servicio.
     */
    public function hiddenRoleNamesFor(?User $actor): array
    {
        if (! $actor || $this->isSuperUser($actor)) {
            return [];
        }

        $actorRank = $this->userRank($actor);

        return Role::query()
            ->pluck('name')
            ->filter(fn (string $roleName) => $this->isProtectedRoleName($roleName) || $this->roleRank($roleName) > $actorRank)
            ->values()
            ->all();
    }

    /**
     * Ejecuta la operación visible role names for del servicio.
     */
    public function visibleRoleNamesFor(?User $actor): array
    {
        if (! $actor) {
            return [];
        }

        return Role::query()
            ->pluck('name')
            ->filter(fn (string $roleName) => $this->canViewRoleName($actor, $roleName))
            ->values()
            ->all();
    }

    /**
     * Ejecuta la operación filter allowed role names del servicio.
     */
    public function filterAllowedRoleNames(?User $actor, array $roleNames): array
    {
        return collect($roleNames)
            ->filter(fn (string $roleName) => $this->canManageRoleName($actor, $roleName))
            ->values()
            ->all();
    }

}
