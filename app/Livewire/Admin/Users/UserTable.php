<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Services\AuthorizationAuditLogger;
use App\Services\AuthorizationHierarchyService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $perPage = 10;
    public string $search = '';
    public string $status = '';
    public string $role = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $pendingDeleteId = null;
    public string $deleteName = '';
    public array $form = [
        'name' => '',
        'lastname' => '',
        'email' => '',
        'number_phone' => '',
        'identity_document' => '',
        'state' => 1,
        'password' => '',
        'roles' => [],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingRole(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        Gate::authorize('users.create');

        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        Gate::authorize('users.update');

        $user = User::query()->with('roles')->findOrFail($id);

        if (! $this->hierarchy()->canManageUser(auth()->user(), $user)) {
            $this->audit()->unauthorized('editar usuario por jerarquía', ['target_user_id' => $user->id]);
            abort(403);
        }

        $this->editingId = $user->id;
        $this->form = [
            'name' => $user->name,
            'lastname' => $user->lastname ?? '',
            'email' => $user->email,
            'number_phone' => $user->number_phone ?? '',
            'identity_document' => $user->identity_document ?? '',
            'state' => (int) $user->state,
            'password' => '',
            'roles' => $user->roles->pluck('name')->values()->all(),
        ];

        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function save(): void
    {
        Gate::authorize($this->editingId ? 'users.update' : 'users.create');
        Gate::authorize('users.assign_roles');

        $actor = auth()->user();
        $validated = $this->validate($this->rules(), attributes: $this->attributes())['form'];
        $this->normalizeNullableFields($validated);

        $user = $this->editingId
            ? User::query()->with('roles')->findOrFail($this->editingId)
            : new User();

        if ($this->editingId && ! $this->hierarchy()->canManageUser($actor, $user)) {
            $this->audit()->unauthorized('guardar usuario por jerarquía', ['target_user_id' => $user->id]);
            abort(403);
        }

        $roles = $this->allowedRolesForSave((array) $validated['roles']);
        if ($roles === []) {
            throw ValidationException::withMessages([
                'form.roles' => __('mma.admin.users.validation.roles_allowed'),
            ]);
        }

        unset($validated['roles']);

        if ($validated['password'] === null) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->fill($validated);
        $user->save();
        $user->syncRoles($roles);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->audit()->record($this->editingId ? 'Usuario actualizado' : 'Usuario creado', [
            'target_user_id' => $user->id,
            'roles' => $roles,
        ]);

        $this->dispatch('successAlert', [
            'success' => $this->editingId
                ? __('mma.admin.users.messages.updated')
                : __('mma.admin.users.messages.created'),
        ]);

        $this->closeModal();
    }

    public function confirmDelete(int $id): void
    {
        Gate::authorize('users.delete');

        $user = User::query()->with('roles')->findOrFail($id);

        if ($user->is(auth()->user())) {
            $this->dispatch('failedAlert', ['failed' => __('mma.admin.users.messages.self_delete_blocked')]);

            return;
        }

        if (! $this->hierarchy()->canManageUser(auth()->user(), $user)) {
            $this->audit()->unauthorized('eliminar usuario por jerarquía', ['target_user_id' => $user->id]);
            abort(403);
        }

        $this->pendingDeleteId = $user->id;
        $this->deleteName = $this->fullName($user);
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        Gate::authorize('users.delete');

        if (! $this->pendingDeleteId) {
            return;
        }

        $user = User::query()->with('roles')->findOrFail($this->pendingDeleteId);

        if ($user->is(auth()->user())) {
            $this->closeDeleteModal();
            $this->dispatch('failedAlert', ['failed' => __('mma.admin.users.messages.self_delete_blocked')]);

            return;
        }

        if (! $this->hierarchy()->canManageUser(auth()->user(), $user)) {
            $this->audit()->unauthorized('confirmar eliminación de usuario por jerarquía', ['target_user_id' => $user->id]);
            abort(403);
        }

        $this->audit()->record('Usuario eliminado', [
            'target_user_id' => $user->id,
            'target_email' => $user->email,
            'roles' => $user->roles->pluck('name')->values()->all(),
        ], 'warning');

        $user->delete();

        $this->closeDeleteModal();
        $this->dispatch('successAlert', ['success' => __('mma.admin.users.messages.deleted')]);
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->pendingDeleteId = null;
        $this->deleteName = '';
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'status', 'role']);
        $this->resetPage();
    }

    public function canManage(User $user): bool
    {
        return $this->hierarchy()->canManageUser(auth()->user(), $user);
    }

    public function roleLabel(string $roleName): string
    {
        $key = "mma.roles.names.{$roleName}";
        $translated = __($key);

        return $translated === $key
            ? str($roleName)->replace('_', ' ')->headline()->toString()
            : $translated;
    }

    public function statusLabel(int $state): string
    {
        return $state === 1
            ? __('mma.admin.common.active')
            : __('mma.admin.common.inactive');
    }

    public function fullName(User $user): string
    {
        return trim((string) $user->name.' '.(string) $user->lastname) ?: $user->email;
    }

    public function render()
    {
        $visibleRoleNames = $this->hierarchy()->visibleRoleNamesFor(auth()->user());

        $users = User::query()
            ->with('roles:id,name')
            ->whereHas('roles', fn ($roleQuery) => $roleQuery->whereIn('name', $visibleRoleNames))
            ->when($this->search !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('name', 'like', "%{$this->search}%")
                        ->orWhere('lastname', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('number_phone', 'like', "%{$this->search}%")
                        ->orWhere('identity_document', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status !== '', fn ($query) => $query->where('state', (int) $this->status))
            ->when($this->role !== '', fn ($query) => $query->whereHas('roles', fn ($roleQuery) => $roleQuery->where('name', $this->role)))
            ->orderBy('name')
            ->orderBy('lastname')
            ->paginate($this->perPage);

        $roles = Role::query()
            ->whereIn('name', $visibleRoleNames)
            ->orderBy('name')
            ->get(['id', 'name']);

        $assignableRoleNames = $this->assignableRoleNames();
        $assignableRoles = Role::query()
            ->whereIn('name', $assignableRoleNames)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.admin.users.user-table', compact('users', 'roles', 'assignableRoles'));
    }

    protected function rules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:100'],
            'form.lastname' => ['nullable', 'string', 'max:100'],
            'form.email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($this->editingId)],
            'form.number_phone' => ['nullable', 'string', 'max:30'],
            'form.identity_document' => ['nullable', 'string', 'max:40'],
            'form.state' => ['required', Rule::in([0, 1])],
            'form.password' => [$this->editingId ? 'nullable' : 'required', 'string', 'min:8', 'max:100'],
            'form.roles' => ['required', 'array', 'min:1'],
            'form.roles.*' => ['string', 'exists:roles,name'],
        ];
    }

    protected function attributes(): array
    {
        return [
            'form.name' => __('mma.admin.users.form.name'),
            'form.lastname' => __('mma.admin.users.form.lastname'),
            'form.email' => __('mma.admin.users.form.email'),
            'form.number_phone' => __('mma.admin.users.form.number_phone'),
            'form.identity_document' => __('mma.admin.users.form.identity_document'),
            'form.state' => __('mma.admin.users.form.state'),
            'form.password' => __('mma.admin.users.form.password'),
            'form.roles' => __('mma.admin.users.form.roles'),
        ];
    }

    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->form = [
            'name' => '',
            'lastname' => '',
            'email' => '',
            'number_phone' => '',
            'identity_document' => '',
            'state' => 1,
            'password' => '',
            'roles' => [],
        ];
        $this->resetErrorBag();
    }

    protected function normalizeNullableFields(array &$validated): void
    {
        foreach (['lastname', 'number_phone', 'identity_document', 'password'] as $field) {
            if (($validated[$field] ?? null) === '') {
                $validated[$field] = null;
            }
        }
    }

    protected function allowedRolesForSave(array $requestedRoles): array
    {
        $actor = auth()->user();
        $hierarchy = $this->hierarchy();

        if ($hierarchy->isSuperUser($actor)) {
            return array_values($requestedRoles);
        }

        return $hierarchy->filterAllowedRoleNames($actor, $requestedRoles);
    }

    protected function assignableRoleNames(): array
    {
        $actor = auth()->user();
        $hierarchy = $this->hierarchy();

        return Role::query()
            ->pluck('name')
            ->filter(fn (string $roleName) => $hierarchy->canManageRoleName($actor, $roleName))
            ->values()
            ->all();
    }

    protected function hierarchy(): AuthorizationHierarchyService
    {
        return app(AuthorizationHierarchyService::class);
    }

    protected function audit(): AuthorizationAuditLogger
    {
        return app(AuthorizationAuditLogger::class);
    }
}
