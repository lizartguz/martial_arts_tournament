<div class="space-y-4">

    {{-- ════════════════════ VISTA: MARCAS ════════════════════ --}}
    @if($view === 'brands')
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">

        {{-- Cabecera --}}
        <div class="tw-panel-header">
            <div>
                <strong class="text-gray-800 dark:text-gray-200">{{ __('messages.catalog.brands.title') }}</strong>
                <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('messages.catalog.brands.subtitle') }}</div>
            </div>
            @can('administration.catalog.view')
            <button type="button" class="tw-btn tw-btn-emerald" wire:click="openBrandForm">
                <i class="fas fa-plus text-xs"></i> {{ __('messages.catalog.brands.add') }}
            </button>
            @endcan
        </div>

        {{-- Formulario de Marca --}}
        @if($showBrandForm)
        <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
            <h6 class="mb-3 font-semibold text-blue-800 dark:text-blue-300">
                {{ $editingBrandId ? __('messages.catalog.brands.edit') : __('messages.catalog.brands.add') }}
            </h6>
            <form wire:submit.prevent="saveBrand">
                <div class="flex flex-wrap gap-3">
                    <div class="flex-1 min-w-48">
                        <label class="tw-label">{{ __('messages.catalog.brands.name') }}</label>
                        <input type="text" class="tw-input" wire:model="brandName"
                               placeholder="{{ __('messages.catalog.brands.name') }}" autofocus>
                        @error('brandName') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="w-36">
                        <label class="tw-label">{{ __('messages.catalog.state_label') }}</label>
                        <select class="tw-input" wire:model="brandState">
                            <option value="1">{{ __('messages.catalog.state_active') }}</option>
                            <option value="0">{{ __('messages.catalog.state_inactive') }}</option>
                        </select>
                        @error('brandState') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="mt-3 flex gap-2">
                    <button type="submit" class="tw-btn tw-btn-emerald"
                            wire:loading.attr="disabled" wire:target="saveBrand"
                            wire:loading.class="opacity-50 cursor-not-allowed">
                        <span wire:loading.remove wire:target="saveBrand">
                            <i class="fas fa-save"></i> {{ __('messages.actions.save') }}
                        </span>
                        <span wire:loading wire:target="saveBrand">
                            <i class="tw-spinner-xs"></i> {{ __('messages.actions.saving') }}
                        </span>
                    </button>
                    <button type="button" class="tw-btn tw-btn-outline" wire:click="closeBrandForm">
                        <i class="fas fa-times"></i> {{ __('messages.actions.cancel') }}
                    </button>
                </div>
            </form>
        </div>
        @endif

        {{-- Filtros --}}
        <div class="mb-4 flex flex-wrap gap-3">
            <div class="w-full sm:w-64">
                <label class="tw-label">{{ __('messages.catalog.brands.search') }}</label>
                <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                       placeholder="{{ __('messages.catalog.brands.search') }}">
            </div>
            <div class="w-full sm:w-28">
                <label class="tw-label">{{ __('messages.authorization.filters.per_page') }}</label>
                <select class="tw-input-sm" wire:model.live="perPage">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="tw-table-wrap">
            <table class="tw-table">
                <thead class="themed-thead">
                    <tr>
                        <th>{{ __('messages.catalog.brands.columns.name') }}</th>
                        <th class="text-center">{{ __('messages.catalog.brands.columns.models') }}</th>
                        <th class="text-center">{{ __('messages.catalog.brands.columns.state') }}</th>
                        <th class="text-center">{{ __('messages.catalog.brands.columns.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($brands as $brand)
                    <tr>
                        <td class="font-medium text-gray-800 dark:text-gray-100">{{ $brand->name }}</td>
                        <td class="text-center">
                            <span class="tw-badge tw-badge-blue">{{ $brand->station_models_count }}</span>
                        </td>
                        <td class="text-center">
                            @if($brand->state == 1)
                                <span class="tw-badge tw-badge-green">{{ __('messages.catalog.state_active') }}</span>
                            @else
                                <span class="tw-badge tw-badge-red">{{ __('messages.catalog.state_inactive') }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex justify-center gap-1">
                                <button type="button" class="tw-btn-xs tw-btn-outline-blue"
                                        wire:click="viewModels({{ $brand->id }})"
                                        title="{{ __('messages.catalog.models.view_models') }}">
                                    <i class="fas fa-list"></i>
                                </button>
                                <button type="button" class="tw-btn-xs tw-btn-outline-amber"
                                        wire:click="editBrand({{ $brand->id }})"
                                        title="{{ __('messages.actions.edit') }}">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button type="button" class="tw-btn-xs tw-btn-outline-red"
                                        onclick="confirmDeleteBrand({{ $brand->id }}, '{{ addslashes($brand->name) }}')"
                                        title="{{ __('messages.actions.delete') }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-gray-500 dark:text-gray-400">
                            {{ __('messages.catalog.brands.empty') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-3">
            {{ $brands->links() }}
        </div>
    </div>
    @endif

    {{-- ════════════════════ VISTA: MODELOS ════════════════════ --}}
    @if($view === 'models')
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">

        {{-- Cabecera --}}
        <div class="tw-panel-header">
            <div class="flex flex-col gap-1">
                <button type="button" class="tw-btn tw-btn-outline self-start" wire:click="backToBrands">
                    <i class="fas fa-arrow-left"></i> {{ __('messages.catalog.models.back') }}
                </button>
                <div class="mt-1">
                    <strong class="text-gray-800 dark:text-gray-200">
                        {{ __('messages.catalog.models.title', ['brand' => $selectedBrandName]) }}
                    </strong>
                    <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('messages.catalog.models.subtitle') }}</div>
                </div>
            </div>
            @can('administration.catalog.view')
            <button type="button" class="tw-btn tw-btn-emerald" wire:click="openModelForm">
                <i class="fas fa-plus text-xs"></i> {{ __('messages.catalog.models.add') }}
            </button>
            @endcan
        </div>

        {{-- Formulario de Modelo --}}
        @if($showModelForm)
        <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
            <h6 class="mb-3 font-semibold text-blue-800 dark:text-blue-300">
                {{ $editingModelId ? __('messages.catalog.models.edit') : __('messages.catalog.models.add') }}
            </h6>
            <form wire:submit.prevent="saveModel">
                <div class="flex flex-wrap gap-3">
                    <div class="flex-1 min-w-48">
                        <label class="tw-label">{{ __('messages.catalog.models.name') }}</label>
                        <input type="text" class="tw-input" wire:model="modelName"
                               placeholder="{{ __('messages.catalog.models.name') }}" autofocus>
                        @error('modelName') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="w-36">
                        <label class="tw-label">{{ __('messages.catalog.state_label') }}</label>
                        <select class="tw-input" wire:model="modelState">
                            <option value="1">{{ __('messages.catalog.state_active') }}</option>
                            <option value="0">{{ __('messages.catalog.state_inactive') }}</option>
                        </select>
                        @error('modelState') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="mt-3 flex gap-2">
                    <button type="submit" class="tw-btn tw-btn-emerald"
                            wire:loading.attr="disabled" wire:target="saveModel"
                            wire:loading.class="opacity-50 cursor-not-allowed">
                        <span wire:loading.remove wire:target="saveModel">
                            <i class="fas fa-save"></i> {{ __('messages.actions.save') }}
                        </span>
                        <span wire:loading wire:target="saveModel">
                            <i class="tw-spinner-xs"></i> {{ __('messages.actions.saving') }}
                        </span>
                    </button>
                    <button type="button" class="tw-btn tw-btn-outline" wire:click="closeModelForm">
                        <i class="fas fa-times"></i> {{ __('messages.actions.cancel') }}
                    </button>
                </div>
            </form>
        </div>
        @endif

        {{-- Filtros --}}
        <div class="mb-4 flex flex-wrap gap-3">
            <div class="w-full sm:w-64">
                <label class="tw-label">{{ __('messages.catalog.models.search') }}</label>
                <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                       placeholder="{{ __('messages.catalog.models.search') }}">
            </div>
            <div class="w-full sm:w-28">
                <label class="tw-label">{{ __('messages.authorization.filters.per_page') }}</label>
                <select class="tw-input-sm" wire:model.live="perPage">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="tw-table-wrap">
            <table class="tw-table">
                <thead class="themed-thead">
                    <tr>
                        <th>{{ __('messages.catalog.models.columns.name') }}</th>
                        <th class="text-center">{{ __('messages.catalog.models.columns.stations') }}</th>
                        <th class="text-center">{{ __('messages.catalog.models.columns.state') }}</th>
                        <th class="text-center">{{ __('messages.catalog.models.columns.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($models as $model)
                    <tr>
                        <td class="font-medium text-gray-800 dark:text-gray-100">{{ $model->name }}</td>
                        <td class="text-center">
                            @if($model->detail_stations_count > 0)
                                <span class="tw-badge tw-badge-amber">{{ $model->detail_stations_count }} {{ __('messages.catalog.models.in_use') }}</span>
                            @else
                                <span class="tw-badge tw-badge-gray">0</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($model->state == 1)
                                <span class="tw-badge tw-badge-green">{{ __('messages.catalog.state_active') }}</span>
                            @else
                                <span class="tw-badge tw-badge-red">{{ __('messages.catalog.state_inactive') }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex justify-center gap-1">
                                <button type="button" class="tw-btn-xs tw-btn-outline-amber"
                                        wire:click="editModel({{ $model->id }})"
                                        title="{{ __('messages.actions.edit') }}">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button type="button" class="tw-btn-xs tw-btn-outline-red"
                                        onclick="confirmDeleteModel({{ $model->id }}, '{{ addslashes($model->name) }}')"
                                        title="{{ __('messages.actions.delete') }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-gray-500 dark:text-gray-400">
                            {{ __('messages.catalog.models.empty') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-3">
            {{ $models->links() }}
        </div>
    </div>
    @endif

</div>

@script
<script>
    window.confirmDeleteBrand = function (id, name) {
        Swal.fire({
            icon: 'warning',
            title: `¿Eliminar la marca "${name}"?`,
            text: 'Esta acción no se puede deshacer. Solo se puede eliminar si no tiene modelos asociados.',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        }).then(result => {
            if (result.isConfirmed) {
                $wire.deleteBrand(id);
            }
        });
    };

    window.confirmDeleteModel = function (id, name) {
        Swal.fire({
            icon: 'warning',
            title: `¿Eliminar el modelo "${name}"?`,
            text: 'Esta acción no se puede deshacer. Solo se puede eliminar si no está asignado a estaciones.',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        }).then(result => {
            if (result.isConfirmed) {
                $wire.deleteModel(id);
            }
        });
    };
</script>
@endscript
