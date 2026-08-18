<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="tw-panel-header">
        <div>
            <strong class="text-gray-800 dark:text-gray-200">{{ __('mma.admin.event_media.table_title') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.event_media.table_subtitle') }}</div>
        </div>
        @can('event_media.create')
            <button type="button" class="tw-btn tw-btn-emerald" wire:click="create">
                <i class="fas fa-plus text-xs"></i>{{ __('mma.admin.event_media.create') }}
            </button>
        @endcan
    </div>

    <div class="mb-4 grid gap-3 xl:grid-cols-[minmax(220px,1fr)_220px_140px_150px_130px_110px_auto]">
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.search') }}</label>
            <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('mma.admin.event_media.search_placeholder') }}">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.event_media.filters.event') }}</label>
            <select class="tw-input-sm" wire:model.live="eventId">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($events as $event)
                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.event_media.filters.file_type') }}</label>
            <select class="tw-input-sm" wire:model.live="fileType">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($this->fileTypeOptions() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.event_media.filters.category') }}</label>
            <select class="tw-input-sm" wire:model.live="category">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($this->categoryOptions() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
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
                    <th>{{ __('mma.admin.event_media.columns.media') }}</th>
                    <th>{{ __('mma.admin.event_media.columns.event') }}</th>
                    <th>{{ __('mma.admin.event_media.columns.category') }}</th>
                    <th>{{ __('mma.admin.event_media.columns.order') }}</th>
                    <th>{{ __('mma.admin.event_media.columns.status') }}</th>
                    <th class="text-center">{{ __('mma.admin.common.columns.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mediaItems as $media)
                    <tr wire:key="event-media-{{ $media->id }}">
                        <td>
                            <div class="flex min-w-[280px] items-center gap-3">
                                <div class="h-14 w-16 flex-shrink-0 overflow-hidden rounded border border-gray-200 bg-gray-100 dark:border-gray-700 dark:bg-gray-700">
                                    @if ($media->file_type === 'image')
                                        <img src="{{ asset($media->file_path) }}" alt="{{ $media->title ?? 'media' }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-gray-400">
                                            <i class="fas fa-play"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $media->title ?? __('mma.admin.event_media.untitled') }}</div>
                                    <div class="text-xs text-gray-500">{{ $this->fileTypeLabel($media->file_type) }}</div>
                                    @if ($media->is_featured)
                                        <span class="tw-badge tw-badge-blue mt-1">{{ __('mma.admin.event_media.featured') }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>{{ $media->event?->name ?? __('mma.admin.common.not_available') }}</div>
                            @if ($media->event?->starts_at)
                                <div class="text-xs text-gray-500">{{ $media->event->starts_at->format('Y-m-d H:i') }}</div>
                            @endif
                        </td>
                        <td>{{ $this->categoryLabel($media->category) }}</td>
                        <td>{{ $media->display_order }}</td>
                        <td>
                            <span class="tw-badge {{ $media->status === 1 ? 'tw-badge-green' : 'tw-badge-gray' }}">
                                {{ $this->statusLabel((int) $media->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="flex justify-center" x-data="tableActionMenu()" @click.outside="close()">
                                <button type="button" class="tw-btn-xs tw-btn-outline" x-ref="trigger" @click="toggle($refs.trigger)">
                                    <i class="fas fa-ellipsis-v"></i>{{ __('mma.admin.common.columns.actions') }}
                                </button>
                                <div x-show="open" x-cloak
                                     x-bind:style="style" class="fixed z-[80] rounded border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                    @can('event_media.update')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                wire:click="edit({{ $media->id }})" @click="open = false">
                                            <i class="fas fa-pencil-alt mr-2 text-blue-500"></i>{{ __('messages.actions.edit') }}
                                        </button>
                                    @endcan
                                    @can('event_media.delete')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                                                wire:click="confirmDelete({{ $media->id }})" @click="open = false">
                                            <i class="fas fa-trash mr-2"></i>{{ __('messages.actions.delete') }}
                                        </button>
                                    @endcan
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

    @if ($mediaItems->hasPages())
        <div class="mt-3">{{ $mediaItems->links() }}</div>
    @endif

    @if ($showModal)
        <x-tw-modal close="closeModal" max-width="5xl" :title="$editingId ? __('mma.admin.event_media.edit') : __('mma.admin.event_media.create')" icon="fas fa-images">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="tw-label">{{ __('mma.admin.event_media.form.event_id') }}</label>
                    <select class="tw-input @error('form.event_id') border-red-500 @enderror" wire:model.live="form.event_id">
                        <option value="">{{ __('mma.admin.common.not_available') }}</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}">{{ $event->name }}</option>
                        @endforeach
                    </select>
                    @error('form.event_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.event_media.form.file_type') }}</label>
                    <select class="tw-input @error('form.file_type') border-red-500 @enderror" wire:model.live="form.file_type">
                        @foreach ($this->fileTypeOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('form.file_type') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.event_media.form.category') }}</label>
                    <select class="tw-input @error('form.category') border-red-500 @enderror" wire:model.live="form.category">
                        @foreach ($this->categoryOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('form.category') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="tw-label">{{ __('mma.admin.event_media.form.title') }}</label>
                    <input type="text" class="tw-input @error('form.title') border-red-500 @enderror" wire:model.live.debounce.300ms="form.title">
                    @error('form.title') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                @if ($form['file_type'] === 'image')
                    <div class="md:col-span-2">
                        <label class="tw-label">{{ __('mma.admin.event_media.form.media_image') }}</label>
                        <input type="file" class="tw-input @error('mediaImage') border-red-500 @enderror" wire:model="mediaImage" accept="image/jpeg,image/png,image/webp">
                        @error('mediaImage') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                        @if ($mediaImage)
                            <img src="{{ $mediaImage->temporaryUrl() }}" alt="{{ __('mma.admin.event_media.form.media_image') }}" class="mt-2 h-40 w-full rounded border border-gray-200 object-cover dark:border-gray-700">
                        @elseif ($currentFilePath && $form['file_type'] === 'image')
                            <img src="{{ asset($currentFilePath) }}" alt="{{ __('mma.admin.event_media.form.media_image') }}" class="mt-2 h-40 w-full rounded border border-gray-200 object-cover dark:border-gray-700">
                        @endif
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.event_media.image_help') }}</p>
                    </div>
                @else
                    <div class="md:col-span-2">
                        <label class="tw-label">{{ __('mma.admin.event_media.form.file_path') }}</label>
                        <input type="text" class="tw-input @error('form.file_path') border-red-500 @enderror" wire:model.live.debounce.300ms="form.file_path" placeholder="https://">
                        @error('form.file_path') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                @endif
                <div>
                    <label class="tw-label">{{ __('mma.admin.event_media.form.display_order') }}</label>
                    <input type="number" min="0" max="9999" class="tw-input @error('form.display_order') border-red-500 @enderror" wire:model.live="form.display_order">
                    @error('form.display_order') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.event_media.form.status') }}</label>
                    <select class="tw-input @error('form.status') border-red-500 @enderror" wire:model.live="form.status">
                        <option value="1">{{ __('mma.admin.common.active') }}</option>
                        <option value="0">{{ __('mma.admin.common.inactive') }}</option>
                    </select>
                    @error('form.status') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" wire:model.live="form.is_featured">
                        <span>{{ __('mma.admin.event_media.form.is_featured') }}</span>
                    </label>
                </div>
                <div class="md:col-span-2">
                    <label class="tw-label">{{ __('mma.admin.event_media.form.description') }}</label>
                    <textarea rows="4" class="tw-input @error('form.description') border-red-500 @enderror" wire:model.live.debounce.400ms="form.description"></textarea>
                    @error('form.description') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
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
        <x-tw-modal close="closeDeleteModal" max-width="md" :title="__('mma.admin.event_media.delete_title')" icon="fas fa-trash" header-class="bg-red-600 text-white">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                {{ __('mma.admin.event_media.delete_warning') }} <strong>{{ $deleteName }}</strong>
            </p>
            <x-slot:footer>
                <button type="button" class="tw-btn tw-btn-gray" wire:click="closeDeleteModal">{{ __('messages.actions.cancel') }}</button>
                <button type="button" class="tw-btn tw-btn-red" wire:click="delete" wire:loading.attr="disabled">{{ __('messages.actions.delete') }}</button>
            </x-slot:footer>
        </x-tw-modal>
    @endif
</div>
