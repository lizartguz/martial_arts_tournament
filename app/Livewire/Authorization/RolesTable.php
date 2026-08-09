<?php

namespace App\Livewire\Authorization;

use App\Services\AuthorizationAuditLogger;
use App\Services\AuthorizationHierarchyService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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
    public string $errorMessage = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $pendingDeleteId = null;
    public string $deleteName = '';
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
     * Prepara el formulario para crear un nuevo rol.
     */
    public function create(): void
    {
        $this->authorizeAction('roles.create');
        $this->resetForm();
        $this->showModal = true;
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
        $permission = $this->editingId ? 'roles.update' : 'roles.create';
        $this->authorizeAction($permission);

        $this->validate([
            'form.name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'name')->ignore($this->editingId),
            ],
            'form.permissions' => ['array'],
            'form.permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $actor = auth()->user();
        $hierarchy = $this->hierarchy();

        if (! $hierarchy->canManageRoleName($actor, $this->form['name'])) {
            $this->audit()->unauthorized('guardar rol protegido', ['role_name' => $this->form['name']]);
            abort(403);
        }

        $role = $this->editingId ? Role::findOrFail($this->editingId) : new Role(['guard_name' => 'web']);

        if ($this->editingId && ! $hierarchy->canManageRoleName($actor, $role->name)) {
            $this->audit()->unauthorized('renombrar rol protegido', ['role_id' => $role->id, 'role_name' => $role->name]);
            abort(403);
        }

        $beforePermissions = $role->exists ? $role->permissions()->pluck('name')->values()->all() : [];
        $beforeName = $role->name;

        $role->name = $this->form['name'];
        $role->guard_name = 'web';
        $role->save();

        $permissions = Permission::query()
            ->whereIn('name', $this->form['permissions'] ?? [])
            ->get();

        $role->syncPermissions($permissions);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $afterPermissions = $role->refresh()->permissions()->pluck('name')->values()->all();

        $this->audit()->record($this->editingId ? 'Rol actualizado' : 'Rol creado', [
            'role_id' => $role->id,
            'before_name' => $beforeName,
            'after_name' => $role->name,
            'before_permissions' => $beforePermissions,
            'after_permissions' => $afterPermissions,
            'added_permissions' => array_values(array_diff($afterPermissions, $beforePermissions)),
            'removed_permissions' => array_values(array_diff($beforePermissions, $afterPermissions)),
        ]);

        $this->successMessage = $this->editingId
            ? __('messages.authorization.roles.messages.updated')
            : __('messages.authorization.roles.messages.created');
        $this->closeModal();
    }

    /**
     * Abre la confirmacion para eliminar un rol permitido.
     */
    public function confirmDelete(int $roleId): void
    {
        $this->authorizeAction('roles.delete');

        $role = Role::findOrFail($roleId);

        if (! $this->hierarchy()->canManageRoleName(auth()->user(), $role->name)) {
            $this->audit()->unauthorized('eliminar rol protegido', ['role_id' => $role->id, 'role_name' => $role->name]);
            abort(403);
        }

        if (auth()->user()?->hasRole($role->name)) {
            $this->errorMessage = __('messages.authorization.roles.messages.self_delete_blocked');
            return;
        }

        $this->pendingDeleteId = $role->id;
        $this->deleteName = $role->name;
        $this->showDeleteModal = true;
    }

    /**
     * Elimina el rol seleccionado y registra la auditoria.
     */
    public function delete(): void
    {
        $this->authorizeAction('roles.delete');

        if (! $this->pendingDeleteId) {
            return;
        }

        $role = Role::with('permissions')->findOrFail($this->pendingDeleteId);

        if (! $this->hierarchy()->canManageRoleName(auth()->user(), $role->name)) {
            $this->audit()->unauthorized('confirmar eliminacion de rol protegido', ['role_id' => $role->id, 'role_name' => $role->name]);
            abort(403);
        }

        if (auth()->user()?->hasRole($role->name)) {
            $this->errorMessage = __('messages.authorization.roles.messages.self_delete_blocked');
            $this->closeDeleteModal();
            return;
        }

        $context = [
            'role_id' => $role->id,
            'role_name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->values()->all(),
        ];

        $role->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->audit()->record('Rol eliminado', $context, 'warning');
        $this->successMessage = __('messages.authorization.roles.messages.deleted');
        $this->closeDeleteModal();
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
     * Cierra el modal de eliminacion y limpia la seleccion pendiente.
     */
    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->pendingDeleteId = null;
        $this->deleteName = '';
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
}
