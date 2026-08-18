<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="tw-panel-header">
        <div>
            <strong class="text-gray-800 dark:text-gray-200">{{ __('mma.admin.weight_classes.table_title') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.weight_classes.table_subtitle') }}</div>
        </div>
        @can('weight_classes.create')
            <button type="button" class="tw-btn tw-btn-emerald" wire:click="create">
                <i class="fas fa-plus text-xs"></i>{{ __('mma.admin.weight_classes.create') }}
            </button>
        @endcan
    </div>

    <div class="mb-4 grid gap-3 md:grid-cols-[minmax(0,1fr)_160px_140px_110px_auto]">
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.search') }}</label>
            <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('mma.admin.weight_classes.search_placeholder') }}">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.weight_classes.filters.gender') }}</label>
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
                    <th>{{ __('mma.admin.weight_classes.columns.name') }}</th>
                    <th>{{ __('mma.admin.weight_classes.columns.gender') }}</th>
                    <th>{{ __('mma.admin.weight_classes.columns.range') }}</th>
                    <th>{{ __('mma.admin.weight_classes.columns.order') }}</th>
                    <th>{{ __('mma.admin.weight_classes.columns.usage') }}</th>
                    <th>{{ __('mma.admin.weight_classes.columns.status') }}</th>
                    <th class="text-center">{{ __('mma.admin.common.columns.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($weightClasses as $weightClass)
                    <tr wire:key="weight-class-{{ $weightClass->id }}">
                        <td>
                            <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $weightClass->name }}</div>
                            <div class="text-xs text-gray-500">{{ $weightClass->slug }}</div>
                        </td>
                        <td>{{ $this->genderLabel($weightClass->gender) }}</td>
                        <td>
                            <span>{{ $weightClass->min_weight_kg ? $weightClass->min_weight_kg . ' kg - ' : '' }}{{ $weightClass->max_weight_kg }} kg</span>
                            @if ($weightClass->max_weight_lb)
                                <span class="block text-xs text-gray-500">{{ $weightClass->max_weight_lb }} lb</span>
                            @endif
                        </td>
                        <td>{{ $weightClass->display_order }}</td>
                        <td>{{ __('mma.admin.weight_classes.usage_summary', ['fighters' => $weightClass->fighters_count, 'fights' => $weightClass->fights_count]) }}</td>
                        <td>
                            <span class="tw-badge {{ $weightClass->status === 1 ? 'tw-badge-green' : 'tw-badge-gray' }}">
                                {{ $this->statusLabel((int) $weightClass->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="flex justify-center" x-data="tableActionMenu()" @click.outside="close()">
                                <button type="button" class="tw-btn-xs tw-btn-outline" x-ref="trigger" @click="toggle($refs.trigger)">
                                    <i class="fas fa-ellipsis-v"></i>{{ __('mma.admin.common.columns.actions') }}
                                </button>
                                <div x-show="open" x-cloak
                                     x-bind:style="style" class="fixed z-[80] rounded border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                    @can('weight_classes.update')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                wire:click="edit({{ $weightClass->id }})" @click="open = false">
                                            <i class="fas fa-pencil-alt mr-2 text-blue-500"></i>{{ __('messages.actions.edit') }}
                                        </button>
                                    @endcan
                                    @can('weight_classes.delete')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                                                wire:click="confirmDelete({{ $weightClass->id }})" @click="open = false">
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

    @if ($weightClasses->hasPages())
        <div class="mt-3">{{ $weightClasses->links() }}</div>
    @endif

    @if ($showModal)
        <x-tw-modal close="closeModal" max-width="3xl" :title="$editingId ? __('mma.admin.weight_classes.edit') : __('mma.admin.weight_classes.create')" icon="fas fa-balance-scale">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="tw-label">{{ __('mma.admin.weight_classes.form.name') }}</label>
                    <input type="text" class="tw-input @error('form.name') border-red-500 @enderror" wire:model.live.debounce.300ms="form.name">
                    @error('form.name') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.weight_classes.form.slug') }}</label>
                    <input type="text" class="tw-input @error('form.slug') border-red-500 @enderror" wire:model.live.debounce.300ms="form.slug">
                    @error('form.slug') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.weight_classes.form.gender') }}</label>
                    <select class="tw-input @error('form.gender') border-red-500 @enderror" wire:model.live="form.gender">
                        <option value="male">{{ __('mma.admin.weight_classes.gender.male') }}</option>
                        <option value="female">{{ __('mma.admin.weight_classes.gender.female') }}</option>
                    </select>
                    @error('form.gender') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.weight_classes.form.status') }}</label>
                    <select class="tw-input @error('form.status') border-red-500 @enderror" wire:model.live="form.status">
                        <option value="1">{{ __('mma.admin.common.active') }}</option>
                        <option value="0">{{ __('mma.admin.common.inactive') }}</option>
                    </select>
                    @error('form.status') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.weight_classes.form.min_weight_kg') }}</label>
                    <input type="number" step="0.01" min="0" class="tw-input @error('form.min_weight_kg') border-red-500 @enderror" wire:model.live="form.min_weight_kg">
                    @error('form.min_weight_kg') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.weight_classes.form.max_weight_kg') }}</label>
                    <input type="number" step="0.01" min="0" class="tw-input @error('form.max_weight_kg') border-red-500 @enderror" wire:model.live="form.max_weight_kg">
                    @error('form.max_weight_kg') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.weight_classes.form.max_weight_lb') }}</label>
                    <input type="number" step="0.01" min="0" class="tw-input @error('form.max_weight_lb') border-red-500 @enderror" wire:model.live="form.max_weight_lb">
                    @error('form.max_weight_lb') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.weight_classes.form.display_order') }}</label>
                    <input type="number" min="1" class="tw-input @error('form.display_order') border-red-500 @enderror" wire:model.live="form.display_order">
                    @error('form.display_order') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
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
        <x-tw-modal close="closeDeleteModal" max-width="md" :title="__('mma.admin.weight_classes.delete_title')" icon="fas fa-trash" header-class="bg-red-600 text-white">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                {{ __('mma.admin.weight_classes.delete_warning') }} <strong>{{ $deleteName }}</strong>
            </p>
            <x-slot:footer>
                <button type="button" class="tw-btn tw-btn-gray" wire:click="closeDeleteModal">{{ __('messages.actions.cancel') }}</button>
                <button type="button" class="tw-btn tw-btn-red" wire:click="delete" wire:loading.attr="disabled">{{ __('messages.actions.delete') }}</button>
            </x-slot:footer>
        </x-tw-modal>
    @endif
</div>
