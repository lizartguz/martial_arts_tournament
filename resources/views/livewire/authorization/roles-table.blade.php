<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    @if ($successMessage)
        <div class="tw-alert tw-alert-green">
            <i class="fas fa-check-circle mt-0.5"></i>
            <span>{{ $successMessage }}</span>
        </div>
    @endif
    @if ($errorMessage)
        <div class="tw-alert tw-alert-red">
            <i class="fas fa-exclamation-triangle mt-0.5"></i>
            <span>{{ $errorMessage }}</span>
        </div>
    @endif

    <div class="tw-panel-header">
        <div>
            <strong class="text-gray-800 dark:text-gray-200">{{ __('messages.authorization.roles.title') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('messages.authorization.roles.subtitle') }}</div>
        </div>
    </div>

    <div class="mb-4 flex flex-wrap gap-3">
        <div class="w-full sm:w-64">
            <label class="tw-label">{{ __('messages.authorization.filters.search') }}</label>
            <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('messages.authorization.roles.search_placeholder') }}">
        </div>
        <div class="w-full sm:w-28">
            <label class="tw-label">{{ __('messages.authorization.filters.per_page') }}</label>
            <select class="tw-input-sm" wire:model.live="perPage">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    <div class="tw-table-wrap">
        <table class="tw-table">
            <thead class="themed-thead">
                <tr>
                    <th>{{ __('messages.authorization.roles.columns.name') }}</th>
                    <th>{{ __('messages.authorization.roles.columns.permissions') }}</th>
                    <th>{{ __('messages.authorization.roles.columns.users') }}</th>
                    <th class="text-center">{{ __('messages.actions.edit') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($roles as $role)
                    <tr>
                        <td>
                            <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $this->roleLabel($role->name) }}</div>
                        </td>
                        <td>{{ $role->permissions_count }}</td>
                        <td>{{ $role->users_count }}</td>
                        <td>
                            <div class="flex justify-center gap-1">
                                @can('roles.update')
                                    <button type="button" class="tw-btn-xs tw-btn-outline-blue" wire:click="edit({{ $role->id }})">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                @endcan
                                @can('roles.delete')
                                    <button type="button" class="tw-btn-xs tw-btn-outline-red" wire:click="confirmDelete({{ $role->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-gray-500">{{ __('messages.authorization.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($roles->hasPages())
        <div class="mt-3">{{ $roles->links() }}</div>
    @endif

    @if ($showModal)
        <x-tw-modal close="closeModal" max-width="5xl" :title="__('messages.authorization.roles.modal_title')" icon="fas fa-user-shield">
            <div class="space-y-4">
                <div>
                    <label class="tw-label">{{ __('messages.authorization.roles.form.name') }}</label>
                    <div class="rounded border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-900/30 dark:text-gray-100">
                        {{ $this->roleLabel($form['name'] ?? '') }}
                    </div>
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <label class="tw-label mb-0">{{ __('messages.authorization.roles.form.permissions') }}</label>
                        <span class="text-xs text-gray-500">{{ count($form['permissions'] ?? []) }} {{ __('messages.authorization.roles.form.selected') }}</span>
                    </div>
                    <div class="grid max-h-[420px] gap-2 overflow-y-auto pr-1 sm:grid-cols-2">
                        @foreach ($allPermissions as $permissionName)
                            <label class="flex min-h-[54px] items-start gap-2 rounded border border-gray-200 bg-gray-50 p-2 text-sm dark:border-gray-700 dark:bg-gray-900/30 {{ $this->canTogglePermission($permissionName) ? '' : 'opacity-70' }}">
                                <input type="checkbox" class="mt-1 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                       value="{{ $permissionName }}" wire:model.live="form.permissions" @disabled(! $this->canTogglePermission($permissionName))>
                                <span>
                                    <span class="block font-medium text-gray-800 dark:text-gray-100">{{ $this->permissionLabel($permissionName) }}</span>
                                    @if (! $this->canTogglePermission($permissionName))
                                        <span class="mt-0.5 block text-xs text-gray-500">{{ __('mma.authorization.protected') }}</span>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('form.permissions') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
            </div>

            <x-slot:footer>
                <button type="button" class="tw-btn tw-btn-gray" wire:click="closeModal">{{ __('messages.actions.cancel') }}</button>
                <button type="button" class="tw-btn tw-btn-emerald" wire:click="save" wire:loading.attr="disabled">
                    <i class="fas fa-save text-xs"></i>{{ __('messages.actions.save') }}
                </button>
            </x-slot:footer>
        </x-tw-modal>
    @endif

    @if ($showDeleteModal)
        <x-tw-modal close="closeDeleteModal" max-width="md" :title="__('messages.authorization.roles.delete_title')" icon="fas fa-trash" header-class="bg-red-600 text-white">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                {{ __('messages.authorization.roles.delete_warning') }} <strong>{{ $deleteName }}</strong>
            </p>
            <x-slot:footer>
                <button type="button" class="tw-btn tw-btn-gray" wire:click="closeDeleteModal">{{ __('messages.actions.cancel') }}</button>
                <button type="button" class="tw-btn tw-btn-red" wire:click="delete" wire:loading.attr="disabled">{{ __('messages.actions.delete') }}</button>
            </x-slot:footer>
        </x-tw-modal>
    @endif
</div>
