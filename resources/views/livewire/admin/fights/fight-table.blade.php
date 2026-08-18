<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="tw-panel-header">
        <div>
            <strong class="text-gray-800 dark:text-gray-200">{{ __('mma.admin.fights.table_title') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.fights.table_subtitle') }}</div>
        </div>
        @can('fights.create')
            <button type="button" class="tw-btn tw-btn-emerald" wire:click="create">
                <i class="fas fa-plus text-xs"></i>{{ __('mma.admin.fights.create') }}
            </button>
        @endcan
    </div>

    <div class="mb-4 grid gap-3 xl:grid-cols-[minmax(220px,1fr)_190px_140px_160px_170px_110px_auto]">
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.search') }}</label>
            <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('mma.admin.fights.search_placeholder') }}">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.fights.filters.event') }}</label>
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
                @foreach ($this->statusOptions() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.fights.filters.bout_type') }}</label>
            <select class="tw-input-sm" wire:model.live="boutType">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($this->boutTypeOptions() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.fights.filters.weight_class') }}</label>
            <select class="tw-input-sm" wire:model.live="weightClassId">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($weightClasses as $weightClass)
                    <option value="{{ $weightClass->id }}">{{ $weightClass->name }}</option>
                @endforeach
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
                    <th>{{ __('mma.admin.fights.columns.fight') }}</th>
                    <th>{{ __('mma.admin.fights.columns.event') }}</th>
                    <th>{{ __('mma.admin.fights.columns.weight_class') }}</th>
                    <th>{{ __('mma.admin.fights.columns.rounds') }}</th>
                    <th>{{ __('mma.admin.fights.columns.order') }}</th>
                    <th>{{ __('mma.admin.fights.columns.status') }}</th>
                    <th class="text-center">{{ __('mma.admin.common.columns.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($fights as $fight)
                    <tr wire:key="fight-{{ $fight->id }}">
                        <td>
                            <div class="flex min-w-[280px] items-center gap-3">
                                <div class="h-12 w-12 flex-shrink-0 overflow-hidden rounded border border-gray-200 bg-gray-100 dark:border-gray-700 dark:bg-gray-700">
                                    @if ($fight->promo_image)
                                        <img src="{{ $fight->promoImageUrl() }}" alt="{{ $fight->title ?? 'fight' }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-gray-400">
                                            <i class="fas fa-fist-raised"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $this->fighterName($fight->cornerRed) }}
                                        <span class="text-xs text-gray-500">{{ __('mma.landing.vs') }}</span>
                                        {{ $this->fighterName($fight->cornerBlue) }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $fight->title ?: $this->boutTypeLabel($fight->bout_type) }}</div>
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        @if ($fight->is_main_event)
                                            <span class="tw-badge tw-badge-green">{{ __('mma.admin.fights.flags.main_event') }}</span>
                                        @endif
                                        @if ($fight->is_featured)
                                            <span class="tw-badge tw-badge-blue">{{ __('mma.admin.fights.flags.featured') }}</span>
                                        @endif
                                        @if ($fight->result_count)
                                            <span class="tw-badge tw-badge-gray">{{ __('mma.admin.fights.flags.has_result') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>{{ $fight->event?->name ?? __('mma.admin.common.not_available') }}</div>
                            @if ($fight->starts_at)
                                <div class="text-xs text-gray-500">{{ $fight->starts_at->format('Y-m-d H:i') }}</div>
                            @endif
                        </td>
                        <td>{{ $fight->weightClass?->name ?? __('mma.admin.common.not_available') }}</td>
                        <td>{{ $fight->rounds }}</td>
                        <td>{{ $fight->display_order }}</td>
                        <td>
                            <span class="tw-badge {{ $this->statusBadge((int) $fight->status) }}">
                                {{ $this->statusLabel((int) $fight->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="flex justify-center" x-data="tableActionMenu()" @click.outside="close()">
                                <button type="button" class="tw-btn-xs tw-btn-outline" x-ref="trigger" @click="toggle($refs.trigger)">
                                    <i class="fas fa-ellipsis-v"></i>{{ __('mma.admin.common.columns.actions') }}
                                </button>
                                <div x-show="open" x-cloak
                                     x-bind:style="style" class="fixed z-[80] rounded border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                    @can('fights.update')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                wire:click="edit({{ $fight->id }})" @click="open = false">
                                            <i class="fas fa-pencil-alt mr-2 text-blue-500"></i>{{ __('messages.actions.edit') }}
                                        </button>
                                    @endcan
                                    @can('fights.delete')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                                                wire:click="confirmDelete({{ $fight->id }})" @click="open = false">
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

    @if ($fights->hasPages())
        <div class="mt-3">{{ $fights->links() }}</div>
    @endif

    @if ($showModal)
        <x-tw-modal close="closeModal" max-width="7xl" :title="$editingId ? __('mma.admin.fights.edit') : __('mma.admin.fights.create')" icon="fas fa-fist-raised">
            <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_320px]">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="md:col-span-2">
                        <label class="tw-label">{{ __('mma.admin.fights.form.event_id') }}</label>
                        <select class="tw-input @error('form.event_id') border-red-500 @enderror" wire:model.live="form.event_id">
                            <option value="">{{ __('mma.admin.common.not_available') }}</option>
                            @foreach ($events as $event)
                                <option value="{{ $event->id }}">{{ $event->name }}</option>
                            @endforeach
                        </select>
                        @error('form.event_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fights.form.status') }}</label>
                        <select class="tw-input @error('form.status') border-red-500 @enderror" wire:model.live="form.status">
                            @foreach ($this->statusOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('form.status') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fights.form.corner_red_fighter_id') }}</label>
                        <select class="tw-input @error('form.corner_red_fighter_id') border-red-500 @enderror" wire:model.live="form.corner_red_fighter_id">
                            <option value="">{{ __('mma.admin.common.not_available') }}</option>
                            @foreach ($fighters as $fighter)
                                <option value="{{ $fighter->id }}">{{ $this->fighterName($fighter) }}</option>
                            @endforeach
                        </select>
                        @error('form.corner_red_fighter_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fights.form.corner_blue_fighter_id') }}</label>
                        <select class="tw-input @error('form.corner_blue_fighter_id') border-red-500 @enderror" wire:model.live="form.corner_blue_fighter_id">
                            <option value="">{{ __('mma.admin.common.not_available') }}</option>
                            @foreach ($fighters as $fighter)
                                <option value="{{ $fighter->id }}">{{ $this->fighterName($fighter) }}</option>
                            @endforeach
                        </select>
                        @error('form.corner_blue_fighter_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fights.form.weight_class_id') }}</label>
                        <select class="tw-input @error('form.weight_class_id') border-red-500 @enderror" wire:model.live="form.weight_class_id">
                            <option value="">{{ __('mma.admin.common.not_available') }}</option>
                            @foreach ($weightClasses as $weightClass)
                                <option value="{{ $weightClass->id }}">{{ $weightClass->name }}</option>
                            @endforeach
                        </select>
                        @error('form.weight_class_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fights.form.title') }}</label>
                        <input type="text" class="tw-input @error('form.title') border-red-500 @enderror" wire:model.live.debounce.300ms="form.title">
                        @error('form.title') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fights.form.bout_type') }}</label>
                        <select class="tw-input @error('form.bout_type') border-red-500 @enderror" wire:model.live="form.bout_type">
                            @foreach ($this->boutTypeOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('form.bout_type') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fights.form.starts_at') }}</label>
                        <input type="datetime-local" class="tw-input @error('form.starts_at') border-red-500 @enderror" wire:model.live="form.starts_at">
                        @error('form.starts_at') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fights.form.rounds') }}</label>
                        <input type="number" min="1" max="12" class="tw-input @error('form.rounds') border-red-500 @enderror" wire:model.live="form.rounds">
                        @error('form.rounds') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fights.form.display_order') }}</label>
                        <input type="number" min="0" class="tw-input @error('form.display_order') border-red-500 @enderror" wire:model.live="form.display_order">
                        @error('form.display_order') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div class="flex items-end gap-4">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" wire:model.live="form.is_main_event">
                            <span>{{ __('mma.admin.fights.form.is_main_event') }}</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" wire:model.live="form.is_featured">
                            <span>{{ __('mma.admin.fights.form.is_featured') }}</span>
                        </label>
                    </div>
                    <div class="md:col-span-3">
                        <label class="tw-label">{{ __('mma.admin.fights.form.notes') }}</label>
                        <textarea rows="5" class="tw-input @error('form.notes') border-red-500 @enderror" wire:model.live.debounce.400ms="form.notes"></textarea>
                        @error('form.notes') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div>
                    <label class="tw-label">{{ __('mma.admin.fights.form.promo_image') }}</label>
                    <input type="file" class="tw-input @error('promoImage') border-red-500 @enderror" wire:model="promoImage" accept="image/jpeg,image/png,image/webp">
                    @error('promoImage') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    @if ($promoImage)
                        <img src="{{ $promoImage->temporaryUrl() }}" alt="{{ __('mma.admin.fights.form.promo_image') }}" class="mt-2 h-40 w-full rounded border border-gray-200 object-cover dark:border-gray-700">
                    @elseif ($currentPromoImage)
                        <img src="{{ \App\Support\PublicMedia::url($currentPromoImage) }}" alt="{{ __('mma.admin.fights.form.promo_image') }}" class="mt-2 h-40 w-full rounded border border-gray-200 object-cover dark:border-gray-700">
                    @endif
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.fights.image_help') }}</p>
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
            :title="__('mma.admin.fights.delete_title')"
            :message="__('mma.admin.fights.delete_warning')"
            :target-name="$deleteName"
        />
    @endif
</div>
