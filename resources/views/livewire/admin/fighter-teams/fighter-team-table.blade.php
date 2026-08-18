<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="tw-panel-header">
        <div>
            <strong class="text-gray-800 dark:text-gray-200">{{ __('mma.admin.fighter_teams.table_title') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.fighter_teams.table_subtitle') }}</div>
        </div>
        @can('fighter_teams.create')
            <button type="button" class="tw-btn tw-btn-emerald" wire:click="create">
                <i class="fas fa-plus text-xs"></i>{{ __('mma.admin.fighter_teams.create') }}
            </button>
        @endcan
    </div>

    <div class="mb-4 grid gap-3 xl:grid-cols-[minmax(220px,1fr)_190px_140px_110px_auto]">
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.search') }}</label>
            <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('mma.admin.fighter_teams.search_placeholder') }}">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.fighter_teams.filters.city') }}</label>
            <select class="tw-input-sm" wire:model.live="cityId">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}">{{ $city->name }}{{ $city->country ? ' · '.$city->country->name : '' }}</option>
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
                    <th>{{ __('mma.admin.fighter_teams.columns.team') }}</th>
                    <th>{{ __('mma.admin.fighter_teams.columns.city') }}</th>
                    <th>{{ __('mma.admin.fighter_teams.columns.coach') }}</th>
                    <th>{{ __('mma.admin.fighter_teams.columns.contact') }}</th>
                    <th>{{ __('mma.admin.fighter_teams.columns.fighters') }}</th>
                    <th>{{ __('mma.admin.fighter_teams.columns.status') }}</th>
                    <th class="text-center">{{ __('mma.admin.common.columns.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($fighterTeams as $team)
                    <tr wire:key="fighter-team-{{ $team->id }}">
                        <td>
                            <div class="flex min-w-[240px] items-center gap-3">
                                <div class="h-12 w-12 flex-shrink-0 overflow-hidden rounded border border-gray-200 bg-gray-100 dark:border-gray-700 dark:bg-gray-700">
                                    @if ($team->logo_path)
                                        <img src="{{ asset($team->logo_path) }}" alt="{{ $team->name }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-gray-400">
                                            <i class="fas fa-dumbbell"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $team->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $team->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $team->city?->name ?? __('mma.admin.common.not_available') }}</td>
                        <td>{{ $team->coach_name ?? __('mma.admin.common.not_available') }}</td>
                        <td>{{ $team->contact_phone ?? __('mma.admin.common.not_available') }}</td>
                        <td><span class="tw-badge tw-badge-blue">{{ $team->fighters_count }}</span></td>
                        <td>
                            <span class="tw-badge {{ (int) $team->status === 1 ? 'tw-badge-green' : 'tw-badge-gray' }}">
                                {{ $this->statusLabel((int) $team->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="flex justify-center" x-data="tableActionMenu()" @click.outside="close()">
                                <button type="button" class="tw-btn-xs tw-btn-outline" x-ref="trigger" @click="toggle($refs.trigger)">
                                    <i class="fas fa-ellipsis-v"></i>{{ __('mma.admin.common.columns.actions') }}
                                </button>
                                <div x-show="open" x-cloak
                                     x-bind:style="style" class="fixed z-[80] rounded border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                    @can('fighter_teams.update')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                wire:click="edit({{ $team->id }})" @click="open = false">
                                            <i class="fas fa-pencil-alt mr-2 text-blue-500"></i>{{ __('messages.actions.edit') }}
                                        </button>
                                    @endcan
                                    @can('fighter_teams.delete')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                                                wire:click="confirmDelete({{ $team->id }})" @click="open = false">
                                            <i class="fas fa-trash mr-2"></i>{{ __('messages.actions.delete') }}
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-gray-500">{{ __('mma.admin.common.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($fighterTeams->hasPages())
        <div class="mt-3">{{ $fighterTeams->links() }}</div>
    @endif

    @if ($showModal)
        <x-tw-modal close="closeModal" max-width="5xl" :title="$editingId ? __('mma.admin.fighter_teams.edit') : __('mma.admin.fighter_teams.create')" icon="fas fa-dumbbell">
            <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_320px]">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighter_teams.form.name') }}</label>
                        <input type="text" class="tw-input @error('form.name') border-red-500 @enderror" wire:model.live.debounce.300ms="form.name">
                        @error('form.name') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighter_teams.form.slug') }}</label>
                        <input type="text" class="tw-input @error('form.slug') border-red-500 @enderror" wire:model.live.debounce.300ms="form.slug">
                        @error('form.slug') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighter_teams.form.city_id') }}</label>
                        <select class="tw-input @error('form.city_id') border-red-500 @enderror" wire:model.live="form.city_id">
                            <option value="">{{ __('mma.admin.common.not_available') }}</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}{{ $city->country ? ' · '.$city->country->name : '' }}</option>
                            @endforeach
                        </select>
                        @error('form.city_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighter_teams.form.status') }}</label>
                        <select class="tw-input @error('form.status') border-red-500 @enderror" wire:model.live="form.status">
                            <option value="1">{{ __('mma.admin.common.active') }}</option>
                            <option value="0">{{ __('mma.admin.common.inactive') }}</option>
                        </select>
                        @error('form.status') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighter_teams.form.coach_name') }}</label>
                        <input type="text" class="tw-input @error('form.coach_name') border-red-500 @enderror" wire:model.live.debounce.300ms="form.coach_name">
                        @error('form.coach_name') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighter_teams.form.contact_phone') }}</label>
                        <input type="text" class="tw-input @error('form.contact_phone') border-red-500 @enderror" wire:model.live.debounce.300ms="form.contact_phone">
                        @error('form.contact_phone') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="tw-label">{{ __('mma.admin.fighter_teams.form.description') }}</label>
                        <textarea rows="7" class="tw-input @error('form.description') border-red-500 @enderror" wire:model.live.debounce.400ms="form.description"></textarea>
                        @error('form.description') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div>
                    <label class="tw-label">{{ __('mma.admin.fighter_teams.form.logo_path') }}</label>
                    <input type="file" class="tw-input @error('logoImage') border-red-500 @enderror" wire:model="logoImage" accept="image/jpeg,image/png,image/webp">
                    @error('logoImage') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    @if ($logoImage)
                        <img src="{{ $logoImage->temporaryUrl() }}" alt="{{ __('mma.admin.fighter_teams.form.logo_path') }}" class="mt-2 h-40 w-full rounded border border-gray-200 object-cover dark:border-gray-700">
                    @elseif ($currentLogoPath)
                        <img src="{{ asset($currentLogoPath) }}" alt="{{ __('mma.admin.fighter_teams.form.logo_path') }}" class="mt-2 h-40 w-full rounded border border-gray-200 object-cover dark:border-gray-700">
                    @endif
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.fighter_teams.image_help') }}</p>
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
        <x-tw-confirm-modal
            close="closeDeleteModal"
            confirm="delete"
            :title="__('mma.admin.fighter_teams.delete_title')"
            :message="__('mma.admin.fighter_teams.delete_warning')"
            :target-name="$deleteName"
        />
    @endif
</div>
