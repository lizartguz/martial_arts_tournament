<?php

namespace App\Livewire\Authorization;

use App\Services\AuthorizationAuditLogger;
use App\Services\AuthorizationHierarchyService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $perPage = 10;
    public string $search = '';
    public string $successMessage = '';
    public bool $showModal = false;
    public ?int $editingId = null;
    public array $form = [
        'name' => '',
        'permissions' => [],
    ];

    /**
     * Reinicia la paginación al cambiar la búsqueda.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación al cambiar el tamaño de página.
     */
    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Carga el registro seleccionado para editarlo.
     */
    public function edit(int $roleId): void
    {
        $this->authorizeAction('roles.update');

        $role = Role::with('permissions')->findOrFail($roleId);

        if (! $this->hierarchy()->canManageRoleName(auth()->user(), $role->name)) {
            $this->audit()->unauthorized('editar rol protegido', ['role_id' => $role->id, 'role_name' => $role->name]);
            abort(403);
        }

        $this->editingId = $role->id;
        $this->form = [
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->values()->all(),
        ];
        $this->resetErrorBag();
        $this->showModal = true;
    }

    /**
     * Valida y guarda los datos del formulario.
     */
    public function save(): void
    {
        $this->authorizeAction('roles.update');

        $this->validate([
            'form.permissions' => ['array'],
            'form.permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $actor = auth()->user();
        $hierarchy = $this->hierarchy();

        $role = Role::with('permissions')->findOrFail($this->editingId);

        if (! $hierarchy->canManageRoleName($actor, $role->name)) {
            $this->audit()->unauthorized('actualizar permisos de rol protegido', ['role_id' => $role->id, 'role_name' => $role->name]);
            abort(403);
        }

        $beforePermissions = $role->permissions->pluck('name')->values()->all();
        $requestedPermissions = $this->permissionsAllowedForSync($beforePermissions, $this->form['permissions'] ?? []);

        $permissions = Permission::query()
            ->whereIn('name', $requestedPermissions)
            ->get();

        $role->syncPermissions($permissions);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $afterPermissions = $role->refresh()->permissions()->pluck('name')->values()->all();

        $this->audit()->record('Permisos de rol actualizados', [
            'role_id' => $role->id,
            'role_name' => $role->name,
            'before_permissions' => $beforePermissions,
            'after_permissions' => $afterPermissions,
            'added_permissions' => array_values(array_diff($afterPermissions, $beforePermissions)),
            'removed_permissions' => array_values(array_diff($beforePermissions, $afterPermissions)),
        ]);

        $this->successMessage = __('messages.authorization.roles.messages.updated');
        $this->closeModal();
    }

    /**
     * Cierra el modal principal y limpia su estado.
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Devuelve roles property solicitado.
     */
    public function getRolesProperty()
    {
        $visibleRoleNames = $this->hierarchy()->visibleRoleNamesFor(auth()->user());
        $rolesTable = config('permission.table_names.roles');
        $modelHasRolesTable = config('permission.table_names.model_has_roles');
        $rolePivotKey = app(PermissionRegistrar::class)->pivotRole;

        return Role::query()
            ->withCount('permissions')
            ->selectSub(
                DB::table($modelHasRolesTable)
                    ->selectRaw('count(*)')
                    ->whereColumn("{$modelHasRolesTable}.{$rolePivotKey}", "{$rolesTable}.id"),
                'users_count'
            )
            ->whereIn('name', $visibleRoleNames)
            ->when($this->search !== '', fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate($this->perPage, ['*'], 'rolesPage');
    }

    /**
     * Devuelve all permissions property solicitado.
     */
    public function getAllPermissionsProperty()
    {
        return Permission::query()
            ->orderBy('name')
            ->pluck('name')
            ->values();
    }

    /**
     * Devuelve la etiqueta visible de role.
     */
    public function roleLabel(string $roleName): string
    {
        $key = "mma.roles.names.{$roleName}";
        $translated = __($key);

        return $translated === $key
            ? Str::headline(str_replace('_', ' ', $roleName))
            : $translated;
    }

    /**
     * Devuelve la etiqueta visible de permission.
     */
    public function permissionLabel(string $permissionName): string
    {
        [$module, $action] = $this->splitPermissionName($permissionName);
        $moduleLabel = __("mma.permissions.modules.{$module}");
        $actionLabel = __("mma.permissions.actions.{$action}");

        if ($moduleLabel === "mma.permissions.modules.{$module}") {
            $moduleLabel = Str::headline(str_replace('_', ' ', $module));
        }

        if ($actionLabel === "mma.permissions.actions.{$action}") {
            $actionLabel = Str::headline(str_replace('_', ' ', $action));
        }

        return trim("{$actionLabel} {$moduleLabel}");
    }

    /**
     * Valida si el usuario puede toggle permission.
     */
    public function canTogglePermission(string $permissionName): bool
    {
        return $this->hierarchy()->canManagePermissionName(auth()->user(), $permissionName);
    }

    /**
     * Renderiza la tabla de roles con filtros activos.
     */
    public function render()
    {
        return view('livewire.authorization.roles-table', [
            'roles' => $this->roles,
            'allPermissions' => $this->allPermissions,
        ]);
    }

    /**
     * Restaura el formulario a sus valores iniciales.
     */
    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->form = [
            'name' => '',
            'permissions' => [],
        ];
        $this->resetErrorBag();
    }

    /**
     * Gestiona authorize action dentro de la tabla de roles.
     */
    protected function authorizeAction(string $permission): void
    {
        if (! auth()->user()?->can($permission)) {
            $this->audit()->unauthorized("permiso requerido {$permission}");
            abort(403);
        }
    }

    /**
     * Gestiona hierarchy dentro de la tabla de roles.
     */
    protected function hierarchy(): AuthorizationHierarchyService
    {
        return app(AuthorizationHierarchyService::class);
    }

    /**
     * Gestiona audit dentro de la tabla de roles.
     */
    protected function audit(): AuthorizationAuditLogger
    {
        return app(AuthorizationAuditLogger::class);
    }

    /**
     * Gestiona permissions allowed for sync dentro de la tabla de roles.
     */
    protected function permissionsAllowedForSync(array $currentPermissions, array $requestedPermissions): array
    {
        $hierarchy = $this->hierarchy();
        $actor = auth()->user();
        $currentProtected = collect($currentPermissions)
            ->filter(fn (string $permission) => ! $hierarchy->canManagePermissionName($actor, $permission))
            ->all();
        $requestedAllowed = collect($requestedPermissions)
            ->filter(fn (string $permission) => $hierarchy->canManagePermissionName($actor, $permission))
            ->all();

        return array_values(array_unique(array_merge($currentProtected, $requestedAllowed)));
    }

    /**
     * Gestiona split permission name dentro de la tabla de roles.
     */
    protected function splitPermissionName(string $permissionName): array
    {
        $parts = explode('.', $permissionName);
        $action = array_pop($parts) ?: $permissionName;
        $module = implode('.', $parts);

        return [$module !== '' ? $module : $permissionName, $action];
    }
}
