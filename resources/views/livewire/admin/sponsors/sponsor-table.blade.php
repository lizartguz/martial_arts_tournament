<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="tw-panel-header">
        <div>
            <strong class="text-gray-800 dark:text-gray-200">{{ __('mma.admin.sponsors.table_title') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.sponsors.table_subtitle') }}</div>
        </div>
        @can('sponsors.create')
            <button type="button" class="tw-btn tw-btn-emerald" wire:click="create">
                <i class="fas fa-plus text-xs"></i>{{ __('mma.admin.sponsors.create') }}
            </button>
        @endcan
    </div>

    <div class="mb-4 grid gap-3 xl:grid-cols-[minmax(220px,1fr)_220px_140px_110px_auto]">
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.search') }}</label>
            <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('mma.admin.sponsors.search_placeholder') }}">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.sponsors.filters.event') }}</label>
            <select class="tw-input-sm" wire:model.live="eventId">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($events as $event)
                    <option value="{{ $event->id }}">{{ $event->name }}</option>
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
                    <th>{{ __('mma.admin.sponsors.columns.sponsor') }}</th>
                    <th>{{ __('mma.admin.sponsors.columns.website') }}</th>
                    <th>{{ __('mma.admin.sponsors.columns.email') }}</th>
                    <th>{{ __('mma.admin.sponsors.columns.events') }}</th>
                    <th>{{ __('mma.admin.sponsors.columns.order') }}</th>
                    <th>{{ __('mma.admin.sponsors.columns.status') }}</th>
                    <th class="text-center">{{ __('mma.admin.common.columns.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sponsors as $sponsor)
                    <tr wire:key="sponsor-{{ $sponsor->id }}">
                        <td>
                            <div class="flex min-w-[260px] items-center gap-3">
                                <div class="h-14 w-16 flex-shrink-0 overflow-hidden rounded border border-gray-200 bg-gray-100 dark:border-gray-700 dark:bg-gray-700">
                                    @if ($sponsor->logo_path)
                                        <img src="{{ $sponsor->logoUrl() }}" alt="{{ $sponsor->name }}" class="h-full w-full object-contain">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-gray-400">
                                            <i class="fas fa-handshake"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $sponsor->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $sponsor->slug }}</div>
                                    @if ($sponsor->description)
                                        <div class="mt-1 max-w-xl text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($sponsor->description, 110) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if ($sponsor->website_url)
                                <a href="{{ $sponsor->website_url }}" target="_blank" rel="noopener noreferrer"
                                   class="text-emerald-700 hover:underline dark:text-emerald-300">
                                    {{ parse_url($sponsor->website_url, PHP_URL_HOST) ?: $sponsor->website_url }}
                                </a>
                            @else
                                {{ __('mma.admin.common.not_available') }}
                            @endif
                        </td>
                        <td>{{ $sponsor->contact_email ?: __('mma.admin.common.not_available') }}</td>
                        <td>{{ __('mma.admin.sponsors.events_summary', ['count' => $sponsor->events_count]) }}</td>
                        <td>{{ $sponsor->display_order }}</td>
                        <td>
                            <span class="tw-badge {{ $sponsor->status === 1 ? 'tw-badge-green' : 'tw-badge-gray' }}">
                                {{ $this->statusLabel((int) $sponsor->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="flex justify-center" x-data="tableActionMenu()" @click.outside="close()">
                                <button type="button" class="tw-btn-xs tw-btn-outline" x-ref="trigger" @click="toggle($refs.trigger)">
                                    <i class="fas fa-ellipsis-v"></i>{{ __('mma.admin.common.columns.actions') }}
                                </button>
                                <div x-show="open" x-cloak
                                     x-bind:style="style" class="fixed z-[80] rounded border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                    @can('sponsors.update')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                wire:click="edit({{ $sponsor->id }})" @click="open = false">
                                            <i class="fas fa-pencil-alt mr-2 text-blue-500"></i>{{ __('messages.actions.edit') }}
                                        </button>
                                    @endcan
                                    @can('sponsors.delete')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                                                wire:click="confirmDelete({{ $sponsor->id }})" @click="open = false">
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

    @if ($sponsors->hasPages())
        <div class="mt-3">{{ $sponsors->links() }}</div>
    @endif

    @if ($showModal)
        <x-tw-modal close="closeModal" max-width="5xl" :title="$editingId ? __('mma.admin.sponsors.edit') : __('mma.admin.sponsors.create')" icon="fas fa-handshake">
            <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_300px]">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="tw-label">{{ __('mma.admin.sponsors.form.name') }}</label>
                        <input type="text" class="tw-input @error('form.name') border-red-500 @enderror" wire:model.live.debounce.300ms="form.name">
                        @error('form.name') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.sponsors.form.slug') }}</label>
                        <input type="text" class="tw-input @error('form.slug') border-red-500 @enderror" wire:model.live.debounce.300ms="form.slug">
                        @error('form.slug') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.sponsors.form.website_url') }}</label>
                        <input type="url" class="tw-input @error('form.website_url') border-red-500 @enderror" wire:model.live.debounce.300ms="form.website_url" placeholder="https://">
                        @error('form.website_url') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.sponsors.form.contact_email') }}</label>
                        <input type="email" class="tw-input @error('form.contact_email') border-red-500 @enderror" wire:model.live.debounce.300ms="form.contact_email">
                        @error('form.contact_email') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.sponsors.form.display_order') }}</label>
                        <input type="number" min="0" max="9999" class="tw-input @error('form.display_order') border-red-500 @enderror" wire:model.live="form.display_order">
                        @error('form.display_order') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.sponsors.form.status') }}</label>
                        <select class="tw-input @error('form.status') border-red-500 @enderror" wire:model.live="form.status">
                            <option value="1">{{ __('mma.admin.common.active') }}</option>
                            <option value="0">{{ __('mma.admin.common.inactive') }}</option>
                        </select>
                        @error('form.status') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="tw-label">{{ __('mma.admin.sponsors.form.description') }}</label>
                        <textarea rows="5" class="tw-input @error('form.description') border-red-500 @enderror" wire:model.live.debounce.400ms="form.description"></textarea>
                        @error('form.description') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="tw-label">{{ __('mma.admin.sponsors.form.events') }}</label>
                        <select multiple size="6" class="tw-input @error('selectedEventIds') border-red-500 @enderror" wire:model.live="selectedEventIds">
                            @foreach ($events as $event)
                                <option value="{{ $event->id }}">{{ $event->name }}</option>
                            @endforeach
                        </select>
                        @error('selectedEventIds') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                        @error('selectedEventIds.*') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.sponsors.events_help') }}</p>
                    </div>
                </div>

                <div>
                    <label class="tw-label">{{ __('mma.admin.sponsors.form.logo_path') }}</label>
                    <input type="file" class="tw-input @error('logoImage') border-red-500 @enderror" wire:model="logoImage" accept="image/jpeg,image/png,image/webp">
                    @error('logoImage') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    <div class="mt-2 h-40 w-full overflow-hidden rounded border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-700">
                        @if ($logoImage)
                            <img src="{{ $logoImage->temporaryUrl() }}" alt="{{ __('mma.admin.sponsors.form.logo_path') }}" class="h-full w-full object-contain">
                        @elseif ($currentLogoPath)
                            <img src="{{ \App\Support\PublicMedia::url($currentLogoPath) }}" alt="{{ __('mma.admin.sponsors.form.logo_path') }}" class="h-full w-full object-contain">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-gray-400">
                                <i class="fas fa-handshake text-xl"></i>
                            </div>
                        @endif
                    </div>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.sponsors.image_help') }}</p>
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
            :title="__('mma.admin.sponsors.delete_title')"
            :message="__('mma.admin.sponsors.delete_warning')"
            :target-name="$deleteName"
        />
    @endif
</div>
