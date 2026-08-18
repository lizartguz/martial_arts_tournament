<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="tw-panel-header">
        <div>
            <strong class="text-gray-800 dark:text-gray-200">{{ __('mma.admin.users.table_title') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.users.table_subtitle') }}</div>
        </div>
        @can('users.create')
            <button type="button" class="tw-btn tw-btn-emerald" wire:click="create">
                <i class="fas fa-plus text-xs"></i>{{ __('mma.admin.users.create') }}
            </button>
        @endcan
    </div>

    <div class="mb-4 grid gap-3 xl:grid-cols-[minmax(220px,1fr)_180px_140px_110px_auto]">
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.search') }}</label>
            <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('mma.admin.users.search_placeholder') }}">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.users.filters.role') }}</label>
            <select class="tw-input-sm" wire:model.live="role">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($roles as $roleOption)
                    <option value="{{ $roleOption->name }}">{{ $this->roleLabel($roleOption->name) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.status') }}</label>
            <select class="tw-input-sm" wire:model.live="status">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                <option value="1">{{ __('mma.admin.common.active') }}</option>
                <option value="0">{{ __('mma.admin.common.inactive') }}</option>
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.per_page') }}</label>
            <select class="tw-input-sm" wire:model.live="perPage">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="button" class="tw-btn tw-btn-outline" wire:click="resetFilters">
                <i class="fas fa-eraser text-xs"></i>{{ __('mma.admin.common.filters.clear') }}
            </button>
        </div>
    </div>

    <div class="tw-table-wrap">
        <table class="tw-table">
            <thead class="themed-thead">
                <tr>
                    <th>{{ __('mma.admin.users.columns.user') }}</th>
                    <th>{{ __('mma.admin.users.columns.contact') }}</th>
                    <th>{{ __('mma.admin.users.columns.roles') }}</th>
                    <th>{{ __('mma.admin.users.columns.last_login') }}</th>
                    <th>{{ __('mma.admin.users.columns.status') }}</th>
                    <th class="text-center">{{ __('mma.admin.common.columns.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr wire:key="user-{{ $user->id }}">
                        <td>
                            <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $this->fullName($user) }}</div>
                            <div class="text-xs text-gray-500">{{ $user->identity_document ?? __('mma.admin.common.not_available') }}</div>
                        </td>
                        <td>
                            <div>{{ $user->email }}</div>
                            <div class="text-xs text-gray-500">{{ $user->number_phone ?? __('mma.admin.common.not_available') }}</div>
                        </td>
                        <td>
                            <div class="flex max-w-[280px] flex-wrap gap-1">
                                @foreach ($user->roles as $assignedRole)
                                    <span class="tw-badge tw-badge-blue">{{ $this->roleLabel($assignedRole->name) }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td>{{ $user->last_login_at?->format('d/m/Y H:i') ?? __('mma.admin.common.not_available') }}</td>
                        <td>
                            <span class="tw-badge {{ (int) $user->state === 1 ? 'tw-badge-green' : 'tw-badge-gray' }}">
                                {{ $this->statusLabel((int) $user->state) }}
                            </span>
                        </td>
                        <td>
                            <div class="flex justify-center" x-data="tableActionMenu()" @click.outside="close()">
                                <button type="button" class="tw-btn-xs tw-btn-outline" x-ref="trigger" @click="toggle($refs.trigger)">
                                    <i class="fas fa-ellipsis-v"></i>{{ __('mma.admin.common.columns.actions') }}
                                </button>
                                <div x-show="open" x-cloak
                                     x-bind:style="style" class="fixed z-[80] rounded border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                    @if ($this->canManage($user))
                                        @can('users.update')
                                            <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                    wire:click="edit({{ $user->id }})" @click="open = false">
                                                <i class="fas fa-pencil-alt mr-2 text-blue-500"></i>{{ __('messages.actions.edit') }}
                                            </button>
                                        @endcan
                                        @can('users.delete')
                                            <button type="button" class="block w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                                                    wire:click="confirmDelete({{ $user->id }})" @click="open = false">
                                                <i class="fas fa-trash mr-2"></i>{{ __('messages.actions.delete') }}
                                            </button>
                                        @endcan
                                    @else
                                        <div class="px-3 py-2 text-xs text-gray-500">{{ __('mma.admin.users.hierarchy_readonly') }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-gray-500">{{ __('mma.admin.common.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($users->hasPages())
        <div class="mt-3">{{ $users->links() }}</div>
    @endif

    @if ($showModal)
        <x-tw-modal close="closeModal" max-width="5xl" :title="$editingId ? __('mma.admin.users.edit') : __('mma.admin.users.create')" icon="fas fa-users-cog">
            <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_320px]">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="tw-label">{{ __('mma.admin.users.form.name') }}</label>
                        <input type="text" class="tw-input @error('form.name') border-red-500 @enderror" wire:model.live.debounce.300ms="form.name">
                        @error('form.name') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.users.form.lastname') }}</label>
                        <input type="text" class="tw-input @error('form.lastname') border-red-500 @enderror" wire:model.live.debounce.300ms="form.lastname">
                        @error('form.lastname') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.users.form.email') }}</label>
                        <input type="email" class="tw-input @error('form.email') border-red-500 @enderror" wire:model.live.debounce.300ms="form.email">
                        @error('form.email') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.users.form.number_phone') }}</label>
                        <input type="text" class="tw-input @error('form.number_phone') border-red-500 @enderror" wire:model.live.debounce.300ms="form.number_phone">
                        @error('form.number_phone') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.users.form.identity_document') }}</label>
                        <input type="text" class="tw-input @error('form.identity_document') border-red-500 @enderror" wire:model.live.debounce.300ms="form.identity_document">
                        @error('form.identity_document') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.users.form.state') }}</label>
                        <select class="tw-input @error('form.state') border-red-500 @enderror" wire:model.live="form.state">
                            <option value="1">{{ __('mma.admin.common.active') }}</option>
                            <option value="0">{{ __('mma.admin.common.inactive') }}</option>
                        </select>
                        @error('form.state') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="tw-label">{{ __('mma.admin.users.form.password') }}</label>
                        <input type="password" class="tw-input @error('form.password') border-red-500 @enderror" wire:model.live.debounce.300ms="form.password" autocomplete="new-password">
                        <p class="mt-1 text-xs text-gray-500">{{ __('mma.admin.users.password_help') }}</p>
                        @error('form.password') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <label class="tw-label mb-0">{{ __('mma.admin.users.form.roles') }}</label>
                        <span class="text-xs text-gray-500">{{ count($form['roles'] ?? []) }} {{ __('mma.admin.users.selected_roles') }}</span>
                    </div>
                    <div class="grid max-h-[360px] gap-2 overflow-y-auto pr-1">
                        @foreach ($assignableRoles as $roleOption)
                            <label class="flex min-h-[46px] items-center gap-2 rounded border border-gray-200 bg-gray-50 p-2 text-sm dark:border-gray-700 dark:bg-gray-900/30">
                                <input type="checkbox" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                       value="{{ $roleOption->name }}" wire:model.live="form.roles">
                                <span class="font-medium text-gray-800 dark:text-gray-100">{{ $this->roleLabel($roleOption->name) }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('form.roles') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
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
        <x-tw-modal close="closeDeleteModal" max-width="md" :title="__('mma.admin.users.delete_title')" icon="fas fa-trash" header-class="bg-red-600 text-white">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                {{ __('mma.admin.users.delete_warning') }} <strong>{{ $deleteName }}</strong>
            </p>
            <x-slot:footer>
                <button type="button" class="tw-btn tw-btn-gray" wire:click="closeDeleteModal">{{ __('messages.actions.cancel') }}</button>
                <button type="button" class="tw-btn tw-btn-red" wire:click="delete" wire:loading.attr="disabled">{{ __('messages.actions.delete') }}</button>
            </x-slot:footer>
        </x-tw-modal>
    @endif
</div>
