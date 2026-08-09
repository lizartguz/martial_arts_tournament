<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;

class AuthorizationHierarchyService
{
    /**
     * Obtiene los roles considerados super administradores.
     */
    public function superRoles(): array
    {
        return (array) config('authorization.super_roles', []);
    }

    /**
     * Obtiene permisos protegidos contra edicion por usuarios no superiores.
     */
    public function protectedPermissions(): array
    {
        return (array) config('authorization.protected_permissions', []);
    }

    /**
     * Devuelve el nivel jerarquico configurado para un rol.
     */
    public function roleRank(string $roleName): int
    {
        return (int) data_get(config('authorization.role_ranks', []), $roleName, 0);
    }

    /**
     * Calcula el mayor nivel jerarquico de los roles del usuario.
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
     * Indica si el usuario pertenece a un rol superior protegido.
     */
    public function isSuperUser(?User $user): bool
    {
        return $user !== null && $user->hasAnyRole($this->superRoles());
    }

    /**
     * Indica si el nombre de rol esta reservado para super administracion.
     */
    public function isProtectedRoleName(string $roleName): bool
    {
        return in_array($roleName, $this->superRoles(), true);
    }

    /**
     * Valida si un usuario puede ver un rol segun la jerarquia.
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
     * Valida si un usuario puede administrar un rol visible para su nivel.
     */
    public function canManageRoleName(?User $actor, string $roleName): bool
    {
        return $this->canViewRoleName($actor, $roleName);
    }

    /**
     * Valida si un permiso puede editarse sin romper reglas protegidas.
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
     * Valida si un usuario puede ver a otro usuario segun sus roles.
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
     * Valida si un usuario puede modificar a otro usuario visible.
     */
    public function canManageUser(?User $actor, ?User $target): bool
    {
        return $this->canViewUser($actor, $target);
    }

    /**
     * Lista roles que deben ocultarse para el usuario actual.
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
     * Lista roles visibles para asignacion o edicion.
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
     * Filtra roles solicitados dejando solo los permitidos al usuario.
     */
    public function filterAllowedRoleNames(?User $actor, array $roleNames): array
    {
        return collect($roleNames)
            ->filter(fn (string $roleName) => $this->canManageRoleName($actor, $roleName))
            ->values()
            ->all();
    }

}
