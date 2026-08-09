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
            <strong class="text-gray-800 dark:text-gray-200">{{ __('messages.authorization.permissions.title') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('messages.authorization.permissions.subtitle') }}</div>
        </div>
        @can('permissions.create')
            <button type="button" class="tw-btn tw-btn-emerald" wire:click="create">
                <i class="fas fa-plus text-xs"></i>{{ __('messages.authorization.permissions.create') }}
            </button>
        @endcan
    </div>

    <div class="mb-4 flex flex-wrap gap-3">
        <div class="w-full sm:w-64">
            <label class="tw-label">{{ __('messages.authorization.filters.search') }}</label>
            <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('messages.authorization.permissions.search_placeholder') }}">
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
                    <th>{{ __('messages.authorization.permissions.columns.name') }}</th>
                    <th>{{ __('messages.authorization.permissions.columns.roles') }}</th>
                    <th>{{ __('messages.authorization.permissions.columns.users') }}</th>
                    <th class="text-center">{{ __('messages.actions.edit') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($permissions as $permission)
                    <tr>
                        <td>
                            <div class="break-all font-semibold text-gray-800 dark:text-gray-100">{{ $permission->name }}</div>
                        </td>
                        <td>{{ $permission->roles_count }}</td>
                        <td>{{ $permission->users_count }}</td>
                        <td>
                            <div class="flex justify-center gap-1">
                                @can('permissions.update')
                                    <button type="button" class="tw-btn-xs tw-btn-outline-blue" wire:click="edit({{ $permission->id }})">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                @endcan
                                @can('permissions.delete')
                                    <button type="button" class="tw-btn-xs tw-btn-outline-red" wire:click="confirmDelete({{ $permission->id }})">
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

    @if ($permissions->hasPages())
        <div class="mt-3">{{ $permissions->links() }}</div>
    @endif

    @if ($showModal)
        <x-tw-modal close="closeModal" max-width="lg" :title="__('messages.authorization.permissions.modal_title')" icon="fas fa-key">
            <div>
                <label class="tw-label">{{ __('messages.authorization.permissions.form.name') }}</label>
                <input type="text" class="tw-input @error('form.name') border-red-500 @enderror" wire:model.live="form.name">
                @error('form.name') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
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
        <x-tw-modal close="closeDeleteModal" max-width="md" :title="__('messages.authorization.permissions.delete_title')" icon="fas fa-trash" header-class="bg-red-600 text-white">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                {{ __('messages.authorization.permissions.delete_warning') }} <strong>{{ $deleteName }}</strong>
            </p>
            <x-slot:footer>
                <button type="button" class="tw-btn tw-btn-gray" wire:click="closeDeleteModal">{{ __('messages.actions.cancel') }}</button>
                <button type="button" class="tw-btn tw-btn-red" wire:click="delete" wire:loading.attr="disabled">{{ __('messages.actions.delete') }}</button>
            </x-slot:footer>
        </x-tw-modal>
    @endif
</div>
