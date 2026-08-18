<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="tw-panel-header">
        <div>
            <strong class="text-gray-800 dark:text-gray-200">{{ __('mma.admin.events.table_title') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.events.table_subtitle') }}</div>
        </div>
        @can('events.create')
            <button type="button" class="tw-btn tw-btn-emerald" wire:click="create">
                <i class="fas fa-plus text-xs"></i>{{ __('mma.admin.events.create') }}
            </button>
        @endcan
    </div>

    <div class="mb-4 grid gap-3 xl:grid-cols-[minmax(220px,1fr)_150px_180px_140px_140px_140px_110px_auto]">
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.search') }}</label>
            <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('mma.admin.events.search_placeholder') }}">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.status') }}</label>
            <select class="tw-input-sm" wire:model.live="status">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($this->statusOptions() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.events.filters.venue') }}</label>
            <select class="tw-input-sm" wire:model.live="venueId">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($venues as $venue)
                    <option value="{{ $venue->id }}">{{ $venue->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.events.filters.featured') }}</label>
            <select class="tw-input-sm" wire:model.live="featured">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                <option value="1">{{ __('mma.admin.common.yes') }}</option>
                <option value="0">{{ __('mma.admin.common.no') }}</option>
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.events.filters.from') }}</label>
            <input type="date" class="tw-input-sm" wire:model.live="dateFrom">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.events.filters.to') }}</label>
            <input type="date" class="tw-input-sm" wire:model.live="dateTo">
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
                    <th>{{ __('mma.admin.events.columns.event') }}</th>
                    <th>{{ __('mma.admin.events.columns.venue') }}</th>
                    <th>{{ __('mma.admin.events.columns.date') }}</th>
                    <th>{{ __('mma.admin.events.columns.content') }}</th>
                    <th>{{ __('mma.admin.events.columns.status') }}</th>
                    <th>{{ __('mma.admin.events.columns.featured') }}</th>
                    <th class="text-center">{{ __('mma.admin.common.columns.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($events as $event)
                    <tr wire:key="event-{{ $event->id }}">
                        <td>
                            <div class="flex min-w-[240px] items-center gap-3">
                                <div class="h-12 w-12 flex-shrink-0 overflow-hidden rounded border border-gray-200 bg-gray-100 dark:border-gray-700 dark:bg-gray-700">
                                    @if ($event->poster_image)
                                        <img src="{{ asset($event->poster_image) }}" alt="{{ $event->name }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-gray-400">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $event->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $event->slug }}</div>
                                    @if ($event->subtitle)
                                        <div class="mt-1 text-xs text-gray-500">{{ $event->subtitle }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $event->venue?->name ?? __('mma.admin.common.not_available') }}</td>
                        <td>
                            <div>{{ $event->starts_at?->format('Y-m-d') }}</div>
                            <div class="text-xs text-gray-500">{{ $event->starts_at?->format('H:i') }}</div>
                        </td>
                        <td>
                            <span class="tw-badge tw-badge-blue">{{ __('mma.admin.events.content_summary', ['fights' => $event->fights_count, 'tickets' => $event->ticket_links_count]) }}</span>
                        </td>
                        <td>
                            <span class="tw-badge {{ $this->statusBadge((int) $event->status) }}">
                                {{ $this->statusLabel((int) $event->status) }}
                            </span>
                        </td>
                        <td>
                            <span class="tw-badge {{ $event->is_featured ? 'tw-badge-green' : 'tw-badge-gray' }}">
                                {{ $event->is_featured ? __('mma.admin.common.yes') : __('mma.admin.common.no') }}
                            </span>
                        </td>
                        <td>
                            <div class="flex justify-center" x-data="tableActionMenu()" @click.outside="close()">
                                <button type="button" class="tw-btn-xs tw-btn-outline" x-ref="trigger" @click="toggle($refs.trigger)">
                                    <i class="fas fa-ellipsis-v"></i>{{ __('mma.admin.common.columns.actions') }}
                                </button>
                                <div x-show="open" x-cloak
                                     x-bind:style="style" class="fixed z-[80] rounded border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                    @can('events.update')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                wire:click="edit({{ $event->id }})" @click="open = false">
                                            <i class="fas fa-pencil-alt mr-2 text-blue-500"></i>{{ __('messages.actions.edit') }}
                                        </button>
                                    @endcan
                                    @can('events.publish')
                                        @if ((int) $event->status !== 1)
                                            <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                    wire:click="publish({{ $event->id }})" @click="open = false">
                                                <i class="fas fa-bullhorn mr-2 text-emerald-500"></i>{{ __('mma.admin.events.actions.publish') }}
                                            </button>
                                        @endif
                                    @endcan
                                    @can('events.delete')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                                                wire:click="confirmDelete({{ $event->id }})" @click="open = false">
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

    @if ($events->hasPages())
        <div class="mt-3">{{ $events->links() }}</div>
    @endif

    @if ($showModal)
        <x-tw-modal close="closeModal" max-width="6xl" :title="$editingId ? __('mma.admin.events.edit') : __('mma.admin.events.create')" icon="fas fa-calendar">
            <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_340px]">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="tw-label">{{ __('mma.admin.events.form.name') }}</label>
                        <input type="text" class="tw-input @error('form.name') border-red-500 @enderror" wire:model.live.debounce.300ms="form.name">
                        @error('form.name') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.events.form.slug') }}</label>
                        <input type="text" class="tw-input @error('form.slug') border-red-500 @enderror" wire:model.live.debounce.300ms="form.slug">
                        @error('form.slug') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="tw-label">{{ __('mma.admin.events.form.subtitle') }}</label>
                        <input type="text" class="tw-input @error('form.subtitle') border-red-500 @enderror" wire:model.live.debounce.300ms="form.subtitle">
                        @error('form.subtitle') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.events.form.venue_id') }}</label>
                        <select class="tw-input @error('form.venue_id') border-red-500 @enderror" wire:model.live="form.venue_id">
                            <option value="">{{ __('mma.admin.common.not_available') }}</option>
                            @foreach ($venues as $venue)
                                <option value="{{ $venue->id }}">{{ $venue->name }}</option>
                            @endforeach
                        </select>
                        @error('form.venue_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.events.form.status') }}</label>
                        <select class="tw-input @error('form.status') border-red-500 @enderror" wire:model.live="form.status">
                            @foreach ($this->statusOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('form.status') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.events.form.starts_at') }}</label>
                        <input type="datetime-local" class="tw-input @error('form.starts_at') border-red-500 @enderror" wire:model.live="form.starts_at">
                        @error('form.starts_at') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.events.form.doors_open_at') }}</label>
                        <input type="datetime-local" class="tw-input @error('form.doors_open_at') border-red-500 @enderror" wire:model.live="form.doors_open_at">
                        @error('form.doors_open_at') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.events.form.timezone') }}</label>
                        <input type="text" class="tw-input @error('form.timezone') border-red-500 @enderror" wire:model.live.debounce.300ms="form.timezone">
                        @error('form.timezone') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div class="flex items-end">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" wire:model.live="form.is_featured">
                            <span>{{ __('mma.admin.events.form.is_featured') }}</span>
                        </label>
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.events.form.stream_url') }}</label>
                        <input type="url" class="tw-input @error('form.stream_url') border-red-500 @enderror" wire:model.live.debounce.300ms="form.stream_url">
                        @error('form.stream_url') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.events.form.ticket_url') }}</label>
                        <input type="url" class="tw-input @error('form.ticket_url') border-red-500 @enderror" wire:model.live.debounce.300ms="form.ticket_url">
                        @error('form.ticket_url') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="tw-label">{{ __('mma.admin.events.form.description') }}</label>
                        <textarea rows="6" class="tw-input @error('form.description') border-red-500 @enderror" wire:model.live.debounce.400ms="form.description"></textarea>
                        @error('form.description') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="tw-label">{{ __('mma.admin.events.form.poster_image') }}</label>
                        <input type="file" class="tw-input @error('posterImage') border-red-500 @enderror" wire:model="posterImage" accept="image/jpeg,image/png,image/webp">
                        @error('posterImage') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                        @if ($posterImage)
                            <img src="{{ $posterImage->temporaryUrl() }}" alt="{{ __('mma.admin.events.form.poster_image') }}" class="mt-2 h-36 w-full rounded border border-gray-200 object-cover dark:border-gray-700">
                        @elseif ($currentPosterImage)
                            <img src="{{ asset($currentPosterImage) }}" alt="{{ __('mma.admin.events.form.poster_image') }}" class="mt-2 h-36 w-full rounded border border-gray-200 object-cover dark:border-gray-700">
                        @endif
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.events.form.banner_image') }}</label>
                        <input type="file" class="tw-input @error('bannerImage') border-red-500 @enderror" wire:model="bannerImage" accept="image/jpeg,image/png,image/webp">
                        @error('bannerImage') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                        @if ($bannerImage)
                            <img src="{{ $bannerImage->temporaryUrl() }}" alt="{{ __('mma.admin.events.form.banner_image') }}" class="mt-2 h-32 w-full rounded border border-gray-200 object-cover dark:border-gray-700">
                        @elseif ($currentBannerImage)
                            <img src="{{ asset($currentBannerImage) }}" alt="{{ __('mma.admin.events.form.banner_image') }}" class="mt-2 h-32 w-full rounded border border-gray-200 object-cover dark:border-gray-700">
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.events.image_help') }}</p>
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
            :title="__('mma.admin.events.delete_title')"
            :message="__('mma.admin.events.delete_warning')"
            :target-name="$deleteName"
        />
    @endif
</div>
