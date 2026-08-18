<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="tw-panel-header">
        <div>
            <strong class="text-gray-800 dark:text-gray-200">{{ __('mma.admin.rankings.table_title') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.rankings.table_subtitle') }}</div>
        </div>
        @can('rankings.update')
            <button type="button" class="tw-btn tw-btn-emerald" wire:click="create">
                <i class="fas fa-plus text-xs"></i>{{ __('mma.admin.rankings.create') }}
            </button>
        @endcan
    </div>

    <div class="mb-4 grid gap-3 xl:grid-cols-[minmax(220px,1fr)_190px_190px_140px_110px_auto]">
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.search') }}</label>
            <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('mma.admin.rankings.search_placeholder') }}">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.rankings.filters.weight_class') }}</label>
            <select class="tw-input-sm" wire:model.live="weightClassId">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($weightClasses as $weightClass)
                    <option value="{{ $weightClass->id }}">{{ $weightClass->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.rankings.filters.gender') }}</label>
            <select class="tw-input-sm" wire:model.live="gender">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                <option value="male">{{ __('mma.admin.weight_classes.gender.male') }}</option>
                <option value="female">{{ __('mma.admin.weight_classes.gender.female') }}</option>
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
                    <th>{{ __('mma.admin.rankings.columns.position') }}</th>
                    <th>{{ __('mma.admin.rankings.columns.fighter') }}</th>
                    <th>{{ __('mma.admin.rankings.columns.weight_class') }}</th>
                    <th>{{ __('mma.admin.rankings.columns.record') }}</th>
                    <th>{{ __('mma.admin.rankings.columns.movement') }}</th>
                    <th>{{ __('mma.admin.rankings.columns.status') }}</th>
                    <th class="text-center">{{ __('mma.admin.common.columns.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rankings as $ranking)
                    <tr wire:key="ranking-{{ $ranking->id }}">
                        <td>
                            <div class="flex min-w-[90px] items-center gap-2">
                                <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">#{{ $ranking->position }}</span>
                                @if ($ranking->is_champion)
                                    <span class="tw-badge tw-badge-green">{{ __('mma.admin.rankings.champion') }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $this->fighterName($ranking->fighter) }}</div>
                            <div class="text-xs text-gray-500">{{ $ranking->fighter?->team?->name ?? __('mma.admin.common.not_available') }}</div>
                        </td>
                        <td>
                            <div>{{ $ranking->weightClass?->name ?? __('mma.admin.common.not_available') }}</div>
                            <div class="text-xs text-gray-500">{{ $this->genderLabel($ranking->gender) }}</div>
                        </td>
                        <td>{{ $this->fighterRecord($ranking->fighter) }}</td>
                        <td>
                            <div>{{ $this->movementLabel($ranking->previous_position, $ranking->position) }}</div>
                            @if ($ranking->ranked_at)
                                <div class="text-xs text-gray-500">{{ $ranking->ranked_at->format('Y-m-d') }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="tw-badge {{ $ranking->status === 1 ? 'tw-badge-green' : 'tw-badge-gray' }}">
                                {{ $this->statusLabel((int) $ranking->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="flex justify-center" x-data="tableActionMenu()" @click.outside="close()">
                                <button type="button" class="tw-btn-xs tw-btn-outline" x-ref="trigger" @click="toggle($refs.trigger)">
                                    <i class="fas fa-ellipsis-v"></i>{{ __('mma.admin.common.columns.actions') }}
                                </button>
                                <div x-show="open" x-cloak
                                     x-bind:style="style" class="fixed z-[80] rounded border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                    @can('rankings.update')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                wire:click="edit({{ $ranking->id }})" @click="open = false">
                                            <i class="fas fa-pencil-alt mr-2 text-blue-500"></i>{{ __('messages.actions.edit') }}
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

    @if ($rankings->hasPages())
        <div class="mt-3">{{ $rankings->links() }}</div>
    @endif

    @if ($showModal)
        <x-tw-modal close="closeModal" max-width="4xl" :title="$editingId ? __('mma.admin.rankings.edit') : __('mma.admin.rankings.create')" icon="fas fa-sort-numeric-up">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="tw-label">{{ __('mma.admin.rankings.form.weight_class_id') }}</label>
                    <select class="tw-input @error('form.weight_class_id') border-red-500 @enderror" wire:model.live="form.weight_class_id">
                        <option value="">{{ __('mma.admin.common.not_available') }}</option>
                        @foreach ($weightClasses as $weightClass)
                            <option value="{{ $weightClass->id }}">{{ $weightClass->name }} · {{ $this->genderLabel($weightClass->gender) }}</option>
                        @endforeach
                    </select>
                    @error('form.weight_class_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.rankings.form.gender') }}</label>
                    <select class="tw-input @error('form.gender') border-red-500 @enderror" wire:model.live="form.gender">
                        <option value="male">{{ __('mma.admin.weight_classes.gender.male') }}</option>
                        <option value="female">{{ __('mma.admin.weight_classes.gender.female') }}</option>
                    </select>
                    @error('form.gender') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="tw-label">{{ __('mma.admin.rankings.form.fighter_id') }}</label>
                    <select class="tw-input @error('form.fighter_id') border-red-500 @enderror" wire:model.live="form.fighter_id">
                        <option value="">{{ __('mma.admin.common.not_available') }}</option>
                        @foreach ($fighters as $fighter)
                            <option value="{{ $fighter->id }}">
                                {{ $this->fighterName($fighter) }} · {{ $fighter->weightClass?->name ?? __('mma.admin.common.not_available') }} · {{ $this->fighterRecord($fighter) }}
                            </option>
                        @endforeach
                    </select>
                    @error('form.fighter_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.rankings.form.position') }}</label>
                    <input type="number" min="1" max="999" class="tw-input @error('form.position') border-red-500 @enderror" wire:model.live="form.position">
                    @error('form.position') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.rankings.form.previous_position') }}</label>
                    <input type="number" min="1" max="999" class="tw-input @error('form.previous_position') border-red-500 @enderror" wire:model.live="form.previous_position">
                    @error('form.previous_position') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.rankings.form.ranked_at') }}</label>
                    <input type="date" class="tw-input @error('form.ranked_at') border-red-500 @enderror" wire:model.live="form.ranked_at">
                    @error('form.ranked_at') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.rankings.form.status') }}</label>
                    <select class="tw-input @error('form.status') border-red-500 @enderror" wire:model.live="form.status">
                        <option value="1">{{ __('mma.admin.common.active') }}</option>
                        <option value="0">{{ __('mma.admin.common.inactive') }}</option>
                    </select>
                    @error('form.status') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" wire:model.live="form.is_champion">
                        <span>{{ __('mma.admin.rankings.form.is_champion') }}</span>
                    </label>
                    @error('form.is_champion') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
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
</div>
