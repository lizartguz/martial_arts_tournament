<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="tw-panel-header">
        <div>
            <strong class="text-gray-800 dark:text-gray-200">{{ __('mma.admin.news.table_title') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.news.table_subtitle') }}</div>
        </div>
        @can('news.create')
            <button type="button" class="tw-btn tw-btn-emerald" wire:click="create">
                <i class="fas fa-plus text-xs"></i>{{ __('mma.admin.news.create') }}
            </button>
        @endcan
    </div>

    <div class="mb-4 grid gap-3 xl:grid-cols-[minmax(220px,1fr)_150px_140px_140px_140px_110px_auto]">
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.search') }}</label>
            <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('mma.admin.news.search_placeholder') }}">
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
            <label class="tw-label">{{ __('mma.admin.news.filters.featured') }}</label>
            <select class="tw-input-sm" wire:model.live="featured">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                <option value="1">{{ __('mma.admin.common.yes') }}</option>
                <option value="0">{{ __('mma.admin.common.no') }}</option>
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.news.filters.from') }}</label>
            <input type="date" class="tw-input-sm" wire:model.live="dateFrom">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.news.filters.to') }}</label>
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
                    <th>{{ __('mma.admin.news.columns.post') }}</th>
                    <th>{{ __('mma.admin.news.columns.author') }}</th>
                    <th>{{ __('mma.admin.news.columns.published_at') }}</th>
                    <th>{{ __('mma.admin.news.columns.status') }}</th>
                    <th>{{ __('mma.admin.news.columns.featured') }}</th>
                    <th class="text-center">{{ __('mma.admin.common.columns.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $post)
                    <tr wire:key="news-post-{{ $post->id }}">
                        <td>
                            <div class="flex min-w-[300px] items-center gap-3">
                                <div class="h-14 w-16 flex-shrink-0 overflow-hidden rounded border border-gray-200 bg-gray-100 dark:border-gray-700 dark:bg-gray-700">
                                    @if ($post->cover_image)
                                        <img src="{{ $post->coverImageUrl() }}" alt="{{ $post->title }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-gray-400">
                                            <i class="fas fa-newspaper"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $post->title }}</div>
                                    <div class="text-xs text-gray-500">{{ $post->slug }}</div>
                                    @if ($post->excerpt)
                                        <div class="mt-1 max-w-xl text-xs text-gray-500">{{ $post->excerpt }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $post->createdBy?->name ?? $post->createdBy?->email ?? __('mma.admin.common.not_available') }}</td>
                        <td>
                            @if ($post->published_at)
                                <div>{{ $post->published_at->format('Y-m-d') }}</div>
                                <div class="text-xs text-gray-500">{{ $post->published_at->format('H:i') }}</div>
                            @else
                                {{ __('mma.admin.common.not_available') }}
                            @endif
                        </td>
                        <td>
                            <span class="tw-badge {{ $this->statusBadge((int) $post->status) }}">
                                {{ $this->statusLabel((int) $post->status) }}
                            </span>
                        </td>
                        <td>
                            <span class="tw-badge {{ $post->is_featured ? 'tw-badge-green' : 'tw-badge-gray' }}">
                                {{ $post->is_featured ? __('mma.admin.common.yes') : __('mma.admin.common.no') }}
                            </span>
                        </td>
                        <td>
                            <div class="flex justify-center" x-data="tableActionMenu()" @click.outside="close()">
                                <button type="button" class="tw-btn-xs tw-btn-outline" x-ref="trigger" @click="toggle($refs.trigger)">
                                    <i class="fas fa-ellipsis-v"></i>{{ __('mma.admin.common.columns.actions') }}
                                </button>
                                <div x-show="open" x-cloak
                                     x-bind:style="style" class="fixed z-[80] rounded border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                    @can('news.update')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                wire:click="edit({{ $post->id }})" @click="open = false">
                                            <i class="fas fa-pencil-alt mr-2 text-blue-500"></i>{{ __('messages.actions.edit') }}
                                        </button>
                                    @endcan
                                    @can('news.publish')
                                        @if ((int) $post->status !== 1)
                                            <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                    wire:click="publish({{ $post->id }})" @click="open = false">
                                                <i class="fas fa-bullhorn mr-2 text-emerald-500"></i>{{ __('mma.admin.news.actions.publish') }}
                                            </button>
                                        @endif
                                    @endcan
                                    @can('news.delete')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                                                wire:click="confirmDelete({{ $post->id }})" @click="open = false">
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

    @if ($posts->hasPages())
        <div class="mt-3">{{ $posts->links() }}</div>
    @endif

    @if ($showModal)
        <x-tw-modal close="closeModal" max-width="7xl" :title="$editingId ? __('mma.admin.news.edit') : __('mma.admin.news.create')" icon="fas fa-newspaper">
            <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_320px]">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="tw-label">{{ __('mma.admin.news.form.title') }}</label>
                        <input type="text" class="tw-input @error('form.title') border-red-500 @enderror" wire:model.live.debounce.300ms="form.title">
                        @error('form.title') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.news.form.slug') }}</label>
                        <input type="text" class="tw-input @error('form.slug') border-red-500 @enderror" wire:model.live.debounce.300ms="form.slug">
                        @error('form.slug') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.news.form.status') }}</label>
                        <select class="tw-input @error('form.status') border-red-500 @enderror" wire:model.live="form.status">
                            @foreach ($this->statusOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('form.status') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.news.form.published_at') }}</label>
                        <input type="datetime-local" class="tw-input @error('form.published_at') border-red-500 @enderror" wire:model.live="form.published_at">
                        @error('form.published_at') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="tw-label">{{ __('mma.admin.news.form.excerpt') }}</label>
                        <textarea rows="3" class="tw-input @error('form.excerpt') border-red-500 @enderror" wire:model.live.debounce.400ms="form.excerpt"></textarea>
                        @error('form.excerpt') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="tw-label">{{ __('mma.admin.news.form.content') }}</label>
                        <textarea rows="10" class="tw-input @error('form.content') border-red-500 @enderror" wire:model.live.debounce.500ms="form.content"></textarea>
                        @error('form.content') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" wire:model.live="form.is_featured">
                            <span>{{ __('mma.admin.news.form.is_featured') }}</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="tw-label">{{ __('mma.admin.news.form.cover_image') }}</label>
                    <input type="file" class="tw-input @error('coverImage') border-red-500 @enderror" wire:model="coverImage" accept="image/jpeg,image/png,image/webp">
                    @error('coverImage') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    @if ($coverImage)
                        <img src="{{ $coverImage->temporaryUrl() }}" alt="{{ __('mma.admin.news.form.cover_image') }}" class="mt-2 h-44 w-full rounded border border-gray-200 object-cover dark:border-gray-700">
                    @elseif ($currentCoverImage)
                        <img src="{{ \App\Support\PublicMedia::url($currentCoverImage) }}" alt="{{ __('mma.admin.news.form.cover_image') }}" class="mt-2 h-44 w-full rounded border border-gray-200 object-cover dark:border-gray-700">
                    @endif
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.news.image_help') }}</p>
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
            :title="__('mma.admin.news.delete_title')"
            :message="__('mma.admin.news.delete_warning')"
            :target-name="$deleteName"
        />
    @endif
</div>
