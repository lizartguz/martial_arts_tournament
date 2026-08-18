<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="tw-panel-header">
        <div>
            <strong class="text-gray-800 dark:text-gray-200">{{ __('mma.admin.venues.table_title') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.venues.table_subtitle') }}</div>
        </div>
        @can('venues.create')
            <button type="button" class="tw-btn tw-btn-emerald" wire:click="create">
                <i class="fas fa-plus text-xs"></i>{{ __('mma.admin.venues.create') }}
            </button>
        @endcan
    </div>

    <div class="mb-4 grid gap-3 xl:grid-cols-[minmax(220px,1fr)_190px_140px_110px_auto]">
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.search') }}</label>
            <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('mma.admin.venues.search_placeholder') }}">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.venues.filters.city') }}</label>
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
                    <th>{{ __('mma.admin.venues.columns.venue') }}</th>
                    <th>{{ __('mma.admin.venues.columns.location') }}</th>
                    <th>{{ __('mma.admin.venues.columns.capacity') }}</th>
                    <th>{{ __('mma.admin.venues.columns.contact') }}</th>
                    <th>{{ __('mma.admin.venues.columns.events') }}</th>
                    <th>{{ __('mma.admin.venues.columns.status') }}</th>
                    <th class="text-center">{{ __('mma.admin.common.columns.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($venues as $venue)
                    <tr wire:key="venue-{{ $venue->id }}">
                        <td>
                            <div class="flex min-w-[250px] items-center gap-3">
                                <div class="h-12 w-12 flex-shrink-0 overflow-hidden rounded border border-gray-200 bg-gray-100 dark:border-gray-700 dark:bg-gray-700">
                                    @if ($venue->image)
                                        <img src="{{ asset($venue->image) }}" alt="{{ $venue->name }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-gray-400">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $venue->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $venue->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>{{ $venue->city?->name ?? __('mma.admin.common.not_available') }}</div>
                            <div class="text-xs text-gray-500">{{ $venue->address ?? __('mma.admin.common.not_available') }}</div>
                        </td>
                        <td>{{ $venue->capacity !== null ? number_format($venue->capacity) : __('mma.admin.common.not_available') }}</td>
                        <td>
                            <div>{{ $venue->contact_name ?? __('mma.admin.common.not_available') }}</div>
                            <div class="text-xs text-gray-500">{{ $venue->contact_phone ?? __('mma.admin.common.not_available') }}</div>
                        </td>
                        <td><span class="tw-badge tw-badge-blue">{{ $venue->events_count }}</span></td>
                        <td>
                            <span class="tw-badge {{ (int) $venue->status === 1 ? 'tw-badge-green' : 'tw-badge-gray' }}">
                                {{ $this->statusLabel((int) $venue->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="flex justify-center" x-data="tableActionMenu()" @click.outside="close()">
                                <button type="button" class="tw-btn-xs tw-btn-outline" x-ref="trigger" @click="toggle($refs.trigger)">
                                    <i class="fas fa-ellipsis-v"></i>{{ __('mma.admin.common.columns.actions') }}
                                </button>
                                <div x-show="open" x-cloak
                                     x-bind:style="style" class="fixed z-[80] rounded border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                    @can('venues.update')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                wire:click="edit({{ $venue->id }})" @click="open = false">
                                            <i class="fas fa-pencil-alt mr-2 text-blue-500"></i>{{ __('messages.actions.edit') }}
                                        </button>
                                    @endcan
                                    @can('venues.delete')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                                                wire:click="confirmDelete({{ $venue->id }})" @click="open = false">
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

    @if ($venues->hasPages())
        <div class="mt-3">{{ $venues->links() }}</div>
    @endif

    @if ($showModal)
        <x-tw-modal close="closeModal" max-width="5xl" :title="$editingId ? __('mma.admin.venues.edit') : __('mma.admin.venues.create')" icon="fas fa-map-marker-alt">
            <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_320px]">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="tw-label">{{ __('mma.admin.venues.form.name') }}</label>
                        <input type="text" class="tw-input @error('form.name') border-red-500 @enderror" wire:model.live.debounce.300ms="form.name">
                        @error('form.name') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.venues.form.slug') }}</label>
                        <input type="text" class="tw-input @error('form.slug') border-red-500 @enderror" wire:model.live.debounce.300ms="form.slug">
                        @error('form.slug') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.venues.form.city_id') }}</label>
                        <select class="tw-input @error('form.city_id') border-red-500 @enderror" wire:model.live="form.city_id">
                            <option value="">{{ __('mma.admin.common.not_available') }}</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}{{ $city->country ? ' · '.$city->country->name : '' }}</option>
                            @endforeach
                        </select>
                        @error('form.city_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.venues.form.status') }}</label>
                        <select class="tw-input @error('form.status') border-red-500 @enderror" wire:model.live="form.status">
                            <option value="1">{{ __('mma.admin.common.active') }}</option>
                            <option value="0">{{ __('mma.admin.common.inactive') }}</option>
                        </select>
                        @error('form.status') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="tw-label">{{ __('mma.admin.venues.form.address') }}</label>
                        <input type="text" class="tw-input @error('form.address') border-red-500 @enderror" wire:model.live.debounce.300ms="form.address">
                        @error('form.address') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.venues.form.latitude') }}</label>
                        <input type="number" step="0.0000001" class="tw-input @error('form.latitude') border-red-500 @enderror" wire:model.live="form.latitude">
                        @error('form.latitude') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.venues.form.longitude') }}</label>
                        <input type="number" step="0.0000001" class="tw-input @error('form.longitude') border-red-500 @enderror" wire:model.live="form.longitude">
                        @error('form.longitude') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.venues.form.capacity') }}</label>
                        <input type="number" min="0" class="tw-input @error('form.capacity') border-red-500 @enderror" wire:model.live="form.capacity">
                        @error('form.capacity') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.venues.form.contact_name') }}</label>
                        <input type="text" class="tw-input @error('form.contact_name') border-red-500 @enderror" wire:model.live.debounce.300ms="form.contact_name">
                        @error('form.contact_name') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.venues.form.contact_phone') }}</label>
                        <input type="text" class="tw-input @error('form.contact_phone') border-red-500 @enderror" wire:model.live.debounce.300ms="form.contact_phone">
                        @error('form.contact_phone') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div>
                    <label class="tw-label">{{ __('mma.admin.venues.form.image') }}</label>
                    <input type="file" class="tw-input @error('venueImage') border-red-500 @enderror" wire:model="venueImage" accept="image/jpeg,image/png,image/webp">
                    @error('venueImage') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    @if ($venueImage)
                        <img src="{{ $venueImage->temporaryUrl() }}" alt="{{ __('mma.admin.venues.form.image') }}" class="mt-2 h-44 w-full rounded border border-gray-200 object-cover dark:border-gray-700">
                    @elseif ($currentImagePath)
                        <img src="{{ asset($currentImagePath) }}" alt="{{ __('mma.admin.venues.form.image') }}" class="mt-2 h-44 w-full rounded border border-gray-200 object-cover dark:border-gray-700">
                    @endif
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.venues.image_help') }}</p>
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
        <x-tw-modal close="closeDeleteModal" max-width="md" :title="__('mma.admin.venues.delete_title')" icon="fas fa-trash" header-class="bg-red-600 text-white">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                {{ __('mma.admin.venues.delete_warning') }} <strong>{{ $deleteName }}</strong>
            </p>
            <x-slot:footer>
                <button type="button" class="tw-btn tw-btn-gray" wire:click="closeDeleteModal">{{ __('messages.actions.cancel') }}</button>
                <button type="button" class="tw-btn tw-btn-red" wire:click="delete" wire:loading.attr="disabled">{{ __('messages.actions.delete') }}</button>
            </x-slot:footer>
        </x-tw-modal>
    @endif
</div>
