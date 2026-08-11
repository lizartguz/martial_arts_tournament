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
     * Reinicia la paginacion al cambiar el texto de busqueda.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginacion al cambiar la cantidad por pagina.
     */
    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Carga un rol existente para editar nombre y permisos.
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
     * Guarda un rol nuevo o actualiza uno existente con sus permisos.
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
     * Cierra el modal principal y limpia el formulario.
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Obtiene roles visibles con conteos para la tabla.
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
     * Obtiene todos los permisos disponibles para asignarlos a roles.
     */
    public function getAllPermissionsProperty()
    {
        return Permission::query()
            ->orderBy('name')
            ->pluck('name')
            ->values();
    }

    /**
     * Devuelve el nombre visible del rol en el idioma activo.
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
     * Devuelve el nombre visible del permiso sin exponer el identificador tecnico.
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
     * Indica si el permiso puede marcarse o desmarcarse por el usuario actual.
     */
    public function canTogglePermission(string $permissionName): bool
    {
        return $this->hierarchy()->canManagePermissionName(auth()->user(), $permissionName);
    }

    /**
     * Renderiza la tabla de roles con permisos disponibles.
     */
    public function render()
    {
        return view('livewire.authorization.roles-table', [
            'roles' => $this->roles,
            'allPermissions' => $this->allPermissions,
        ]);
    }

    /**
     * Restaura el estado inicial del formulario de rol.
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
     * Verifica que el usuario tenga el permiso requerido.
     */
    protected function authorizeAction(string $permission): void
    {
        if (! auth()->user()?->can($permission)) {
            $this->audit()->unauthorized("permiso requerido {$permission}");
            abort(403);
        }
    }

    /**
     * Devuelve el servicio de jerarquia de autorizacion.
     */
    protected function hierarchy(): AuthorizationHierarchyService
    {
        return app(AuthorizationHierarchyService::class);
    }

    /**
     * Devuelve el servicio de auditoria de autorizacion.
     */
    protected function audit(): AuthorizationAuditLogger
    {
        return app(AuthorizationAuditLogger::class);
    }

    /**
     * Mantiene intactos los permisos protegidos cuando el actor no puede editarlos.
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

    protected function splitPermissionName(string $permissionName): array
    {
        $parts = explode('.', $permissionName);
        $action = array_pop($parts) ?: $permissionName;
        $module = implode('.', $parts);

        return [$module !== '' ? $module : $permissionName, $action];
    }
}
