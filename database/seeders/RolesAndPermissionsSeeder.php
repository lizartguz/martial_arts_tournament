<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Limpia y reconstruye roles y permisos base del sistema.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        DB::transaction(function () {
            $this->clearSpatieAuthorizationData();

            $this->createPermissions();
            $this->createRoles();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Elimina relaciones, roles y permisos previos de Spatie.
     */
    protected function clearSpatieAuthorizationData(): void
    {
        DB::table('model_has_permissions')->delete();
        DB::table('model_has_roles')->delete();
        DB::table('role_has_permissions')->delete();
        Role::query()->delete();
        Permission::query()->delete();
    }

    /**
     * Crea los permisos estructurados por modulo del menu.
     */
    protected function createPermissions(): array
    {
        $permissions = [
            'authorization.access',

            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',

            'permissions.view',
            'permissions.create',
            'permissions.update',
            'permissions.delete',

            'notifications.view',
            'subscriptions.view',
            'sales_subscribers.view',

            'user_management.view',
            'pay_renewals.view',

            'stations.table.view',
            'stations.map.view',
            'stations.charts.view',
            'stations.downloads.view',

            'agro.chill_hours.view',
            'agro.gdd.view',
            'agro.early_warning.view',

            'data.import_global.view',
            'data.organize_users.view',

            'administration.forecasts_edit.view',
            'administration.logs.view',
            'administration.catalog.view',
        ];

        $permissions = array_values(array_unique($permissions));

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        return $permissions;
    }

    /**
     * Crea roles base y asigna permisos segun su alcance operativo.
     */
    protected function createRoles(): void
    {
        $allPermissions = Permission::query()->pluck('name')->all();

        $superRoles = [
            'super_manager',
        ];

        foreach ($superRoles as $roleName) {
            Role::create(['name' => $roleName, 'guard_name' => 'web'])
                ->syncPermissions($allPermissions);
        }

        Role::create(['name' => 'admin', 'guard_name' => 'web'])
            ->syncPermissions($allPermissions);

        $stationPermissions = [
            'stations.table.view',
            'stations.map.view',
            'stations.charts.view',
            'stations.downloads.view',
        ];

        $agroPermissions = [
            'agro.chill_hours.view',
            'agro.gdd.view',
            'agro.early_warning.view',
        ];

        $operationalPermissions = [
            'subscriptions.view',
            'sales_subscribers.view',
            'user_management.view',
            'administration.forecasts_edit.view',
            ...$stationPermissions,
            ...$agroPermissions,
        ];

        Role::create(['name' => 'manager', 'guard_name' => 'web'])
            ->syncPermissions($operationalPermissions);

        Role::create(['name' => 'meteorology', 'guard_name' => 'web'])
            ->syncPermissions($operationalPermissions);

        Role::create(['name' => 'meteorologist', 'guard_name' => 'web'])
            ->syncPermissions($operationalPermissions);

        Role::create(['name' => 'technical', 'guard_name' => 'web'])
            ->syncPermissions([
                'user_management.view',
                ...$stationPermissions,
                ...$agroPermissions,
            ]);

        Role::create(['name' => 'sales', 'guard_name' => 'web'])
            ->syncPermissions([
                'user_management.view',
                'sales_subscribers.view',
                'stations.table.view',
                'stations.map.view',
            ]);

        Role::create(['name' => 'marketing', 'guard_name' => 'web'])
            ->syncPermissions([
                'notifications.view',
            ]);

        Role::create(['name' => 'subscriber', 'guard_name' => 'web'])
            ->syncPermissions([
                'user_management.view',
                'pay_renewals.view',
                ...$stationPermissions,
                ...$agroPermissions,
            ]);
    }

}
