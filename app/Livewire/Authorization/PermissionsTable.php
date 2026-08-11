<?php

namespace App\Livewire\Authorization;

use App\Services\AuthorizationAuditLogger;
use App\Services\AuthorizationHierarchyService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionsTable extends Component
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
     * Abre el formulario para crear un registro.
     */
    public function create(): void
    {
        $this->authorizeAction('permissions.create');
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Carga el registro seleccionado para editarlo.
     */
    public function edit(int $permissionId): void
    {
        $this->authorizeAction('permissions.update');

        $permission = Permission::findOrFail($permissionId);

        if (! $this->hierarchy()->canManagePermissionName(auth()->user(), $permission->name)) {
            $this->audit()->unauthorized('editar permiso protegido', [
                'permission_id' => $permission->id,
                'permission_name' => $permission->name,
            ]);
            abort(403);
        }

        $this->editingId = $permission->id;
        $this->form = ['name' => $permission->name];
        $this->resetErrorBag();
        $this->showModal = true;
    }

    /**
     * Valida y guarda los datos del formulario.
     */
    public function save(): void
    {
        $permissionName = $this->editingId ? 'permissions.update' : 'permissions.create';
        $this->authorizeAction($permissionName);

        $this->validate([
            'form.name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('permissions', 'name')->ignore($this->editingId),
            ],
        ]);

        $actor = auth()->user();
        $hierarchy = $this->hierarchy();
        $permission = $this->editingId ? Permission::findOrFail($this->editingId) : new Permission(['guard_name' => 'web']);

        if ($this->editingId && ! $hierarchy->canManagePermissionName($actor, $permission->name)) {
            $this->audit()->unauthorized('renombrar permiso protegido', [
                'permission_id' => $permission->id,
                'permission_name' => $permission->name,
            ]);
            abort(403);
        }

        if (! $hierarchy->canManagePermissionName($actor, $this->form['name']) && ! $hierarchy->isSuperUser($actor)) {
            $this->audit()->unauthorized('crear permiso protegido', [
                'permission_name' => $this->form['name'],
            ]);
            abort(403);
        }

        $beforeName = $permission->name;
        $permission->name = $this->form['name'];
        $permission->guard_name = 'web';
        $permission->save();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->audit()->record($this->editingId ? 'Permiso actualizado' : 'Permiso creado', [
            'permission_id' => $permission->id,
            'before_name' => $beforeName,
            'after_name' => $permission->name,
        ]);

        $this->successMessage = $this->editingId
            ? __('messages.authorization.permissions.messages.updated')
            : __('messages.authorization.permissions.messages.created');
        $this->closeModal();
    }

    /**
     * Prepara la confirmación de eliminación del registro.
     */
    public function confirmDelete(int $permissionId): void
    {
        $this->authorizeAction('permissions.delete');

        $permission = Permission::findOrFail($permissionId);

        if (! $this->hierarchy()->canManagePermissionName(auth()->user(), $permission->name)) {
            $this->audit()->unauthorized('eliminar permiso protegido', [
                'permission_id' => $permission->id,
                'permission_name' => $permission->name,
            ]);
            abort(403);
        }

        $this->pendingDeleteId = $permission->id;
        $this->deleteName = $permission->name;
        $this->showDeleteModal = true;
    }

    /**
     * Elimina el registro seleccionado cuando está permitido.
     */
    public function delete(): void
    {
        $this->authorizeAction('permissions.delete');

        if (! $this->pendingDeleteId) {
            return;
        }

        $permission = Permission::findOrFail($this->pendingDeleteId);

        if (! $this->hierarchy()->canManagePermissionName(auth()->user(), $permission->name)) {
            $this->audit()->unauthorized('confirmar eliminacion de permiso protegido', [
                'permission_id' => $permission->id,
                'permission_name' => $permission->name,
            ]);
            abort(403);
        }

        $context = [
            'permission_id' => $permission->id,
            'permission_name' => $permission->name,
        ];

        $permission->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->audit()->record('Permiso eliminado', $context, 'warning');
        $this->successMessage = __('messages.authorization.permissions.messages.deleted');
        $this->closeDeleteModal();
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
     * Cierra el modal de eliminación.
     */
    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->pendingDeleteId = null;
        $this->deleteName = '';
    }

    /**
     * Devuelve permissions property solicitado.
     */
    public function getPermissionsProperty()
    {
        $permissionsTable = config('permission.table_names.permissions');
        $modelHasPermissionsTable = config('permission.table_names.model_has_permissions');
        $permissionPivotKey = app(PermissionRegistrar::class)->pivotPermission;

        return Permission::query()
            ->withCount('roles')
            ->selectSub(
                DB::table($modelHasPermissionsTable)
                    ->selectRaw('count(*)')
                    ->whereColumn("{$modelHasPermissionsTable}.{$permissionPivotKey}", "{$permissionsTable}.id"),
                'users_count'
            )
            ->when($this->search !== '', fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate($this->perPage, ['*'], 'permissionsPage');
    }

    /**
     * Renderiza la tabla de permissions con filtros activos.
     */
    public function render()
    {
        return view('livewire.authorization.permissions-table', [
            'permissions' => $this->permissions,
        ]);
    }

    /**
     * Restaura el formulario a sus valores iniciales.
     */
    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->form = ['name' => ''];
        $this->resetErrorBag();
    }

    /**
     * Gestiona authorize action dentro de la tabla de permissions.
     */
    protected function authorizeAction(string $permission): void
    {
        if (! auth()->user()?->can($permission)) {
            $this->audit()->unauthorized("permiso requerido {$permission}");
            abort(403);
        }
    }

    /**
     * Gestiona hierarchy dentro de la tabla de permissions.
     */
    protected function hierarchy(): AuthorizationHierarchyService
    {
        return app(AuthorizationHierarchyService::class);
    }

    /**
     * Gestiona audit dentro de la tabla de permissions.
     */
    protected function audit(): AuthorizationAuditLogger
    {
        return app(AuthorizationAuditLogger::class);
    }
}
