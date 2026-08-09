<div>
    {{-- Filtros --}}
    <div class="mb-4 border-b border-gray-200 pb-3 dark:border-gray-700">
        <div class="flex flex-wrap items-end gap-2">
            <div class="flex-1 min-w-48">
                <input type="text" list="list_stations" wire:model="searchStation" class="tw-input-sm" placeholder="Nombre de estación">
                <datalist id="list_stations">
                    @foreach($stations as $item)
                        <option value="{{ $item->name }}"></option>
                    @endforeach
                </datalist>
            </div>
            <div class="flex flex-wrap gap-1">
                <button class="tw-btn tw-btn-emerald" wire:click='viewData'>Mostrar</button>
                <button class="tw-btn tw-btn-amber" id="saveChangesBtn" wire:click=''>Guardar cambios</button>
                <button class="tw-btn tw-btn-gray" wire:click=''>Calcular</button>
                <button class="tw-btn tw-btn-blue" wire:click=''>Calcular alertas</button>
            </div>
            <div class="ml-auto">
                @if(!$formAdd)
                    <button class="tw-btn tw-btn-emerald" wire:click='visibleAdd'>Nuevo pronóstico</button>
                @else
                    <button class="tw-btn tw-btn-gray" wire:click='noVisibleAdd'>Cerrar</button>
                @endif
            </div>
        </div>
    </div>

    {{-- Formulario nuevo pronóstico --}}
    @if($formAdd)
        <div class="mb-4 rounded border border-gray-300 bg-gray-400 p-3">
            <div class="flex flex-wrap gap-2 pb-3">
                @foreach([
                    ['id'=>'v10mAU',    'model'=>'v10mAU',    'label'=>'V10M'],
                    ['id'=>'v10mdirAU', 'model'=>'v10mdirAU', 'label'=>'V10M DIR'],
                    ['id'=>'precAU',    'model'=>'precAU',    'label'=>'PREC'],
                    ['id'=>'capeAU',    'model'=>'capeAU',    'label'=>'CAPE'],
                    ['id'=>'liAU',      'model'=>'liAU',      'label'=>'LI'],
                    ['id'=>'t2mAU',     'model'=>'t2mAU',     'label'=>'T2M'],
                    ['id'=>'hr2mAU',    'model'=>'hr2mAU',    'label'=>'HR2M'],
                    ['id'=>'baroAU',    'model'=>'baroAU',    'label'=>'BARO'],
                    ['id'=>'cloudAU',   'model'=>'cloudAU',   'label'=>'NUBES'],
                    ['id'=>'snowAU',    'model'=>'snowAU',    'label'=>'NIEVE'],
                    ['id'=>'dpAU',      'model'=>'dpAU',      'label'=>'DP'],
                ] as $field)
                    <div class="flex flex-col" style="min-width:5rem">
                        <label class="tw-label">{{ $field['label'] }}</label>
                        <input type="text" id="{{ $field['id'] }}" wire:model="{{ $field['model'] }}" class="tw-input-sm">
                    </div>
                @endforeach
            </div>
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex flex-col" style="min-width:14rem">
                    <label class="tw-label">FECHA</label>
                    <input type="datetime-local" wire:model="reg_dateAU" id="reg_dateA" class="tw-input-sm">
                </div>
                <button class="tw-btn tw-btn-emerald" wire:click='addForecast'>Guardar</button>
            </div>
        </div>
    @endif

    {{-- Tabla --}}
    <div class="tw-table-wrap">
        <table id="registrosTable" class="tw-table">
            <thead class="themed-thead">
                <tr>
                    <th>Nro</th>
                    <th>FECHA</th>
                    <th>V10M</th>
                    <th>V10M DIR</th>
                    <th>PREC</th>
                    <th>CAPE</th>
                    <th>LI</th>
                    <th>T2M</th>
                    <th>HR2M</th>
                    <th>BARO</th>
                    <th>NUBES</th>
                    <th>NIEVE</th>
                    <th>DP</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @if(count($data) > 0)
                    @for($i = 0; $i < count($data); $i++)
                    <tr data-id="{{ $data[$i]->id }}">
                        <td>{{ $i + 1 }}</td>
                        <td contenteditable="true" class="editable" data-column="{{ 'col_'.$data[$i]->id }}">{{ $data[$i]->reg_date }}</td>
                        <td contenteditable="true" class="editable" data-column="{{ 'col_'.$data[$i]->id }}">{{ $data[$i]->v10m }}</td>
                        <td contenteditable="true" class="editable" data-column="{{ 'col_'.$data[$i]->id }}">{{ $data[$i]->v10m_dir }}</td>
                        <td contenteditable="true" class="editable" data-column="{{ 'col_'.$data[$i]->id }}">{{ $data[$i]->prec }}</td>
                        <td contenteditable="true" class="editable" data-column="{{ 'col_'.$data[$i]->id }}">{{ $data[$i]->cape }}</td>
                        <td contenteditable="true" class="editable" data-column="{{ 'col_'.$data[$i]->id }}">{{ $data[$i]->li }}</td>
                        <td contenteditable="true" class="editable" data-column="{{ 'col_'.$data[$i]->id }}">{{ $data[$i]->t2m }}</td>
                        <td contenteditable="true" class="editable" data-column="{{ 'col_'.$data[$i]->id }}">{{ $data[$i]->hr2m }}</td>
                        <td contenteditable="true" class="editable" data-column="{{ 'col_'.$data[$i]->id }}">{{ $data[$i]->baro }}</td>
                        <td contenteditable="true" class="editable" data-column="{{ 'col_'.$data[$i]->id }}">{{ $data[$i]->cloud }}</td>
                        <td contenteditable="true" class="editable" data-column="{{ 'col_'.$data[$i]->id }}">{{ $data[$i]->snow }}</td>
                        <td contenteditable="true" class="editable" data-column="{{ 'col_'.$data[$i]->id }}">{{ $data[$i]->dp }}</td>
                        <td class="whitespace-nowrap">
                            <button class="tw-btn-xs tw-btn-amber" wire:click="formUpdateModal('{{{json_encode($data[$i])}}}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="tw-btn-xs tw-btn-red"
                                wire:click="delete('{{$data[$i]->id}}')"
                                wire:confirm="¿Está seguro de eliminar este pronóstico?">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                    @endfor
                @else
                    <tr>
                        <td colspan="15" class="py-6 text-center text-gray-500 dark:text-gray-400">No hay datos</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Modal de actualización (Tailwind/Alpine) --}}
    <div x-data="{ open: false }"
         x-on:open-forecasts-modal.window="open = true"
         x-on:close-forecasts-modal.window="open = false"
         x-on:keydown.escape.window="open = false"
         x-show="open" x-cloak
         class="fixed inset-0 z-[60] overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black/50"></div>
        <div class="relative flex min-h-full items-start justify-center p-4 sm:p-6">
            <div class="relative w-full max-w-6xl rounded-lg bg-white shadow-xl dark:bg-gray-800 dark:text-gray-100">
                <div class="flex items-center justify-between rounded-t-lg border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                    <h5 class="m-0 text-base font-semibold">{{ $searchStation }}</h5>
                    <button type="button" class="text-gray-500 hover:text-gray-700" x-on:click="open = false" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="max-h-[75vh] overflow-y-auto p-4">
                    <div class="flex flex-wrap justify-center gap-2">
                        @foreach([
                            ['id'=>'v10mAU',    'model'=>'v10mAU',    'label'=>'V10M'],
                            ['id'=>'v10mdirAU', 'model'=>'v10mdirAU', 'label'=>'V10M DIR'],
                            ['id'=>'precAU',    'model'=>'precAU',    'label'=>'PREC'],
                            ['id'=>'capeAU',    'model'=>'capeAU',    'label'=>'CAPE'],
                            ['id'=>'liAU',      'model'=>'liAU',      'label'=>'LI'],
                            ['id'=>'t2mAU',     'model'=>'t2mAU',     'label'=>'T2M'],
                            ['id'=>'hr2mAU',    'model'=>'hr2mAU',    'label'=>'HR2M'],
                            ['id'=>'baroAU',    'model'=>'baroAU',    'label'=>'BARO'],
                            ['id'=>'cloudAU',   'model'=>'cloudAU',   'label'=>'NUBES'],
                            ['id'=>'snowAU',    'model'=>'snowAU',    'label'=>'NIEVE'],
                            ['id'=>'dpAU',      'model'=>'dpAU',      'label'=>'DP'],
                        ] as $field)
                            <div class="flex flex-col" style="min-width:5rem">
                                <label class="tw-label">{{ $field['label'] }}</label>
                                <input type="text" id="{{ $field['id'] }}" wire:model="{{ $field['model'] }}" class="tw-input-sm">
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 flex flex-wrap justify-center gap-4">
                        <div class="flex flex-col" style="min-width:14rem">
                            <label class="tw-label">FECHA REGISTRADA</label>
                            <input type="datetime-local" wire:model="reg_dateAU" id="reg_dateAU" class="tw-input-sm" disabled>
                        </div>
                        <div class="flex flex-col" style="min-width:14rem">
                            <label class="tw-label">NUEVA FECHA</label>
                            <input type="datetime-local" wire:model="new_reg_dateAU" id="new_reg_dateAU" class="tw-input-sm">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-2 border-t border-gray-200 px-4 py-3 dark:border-gray-700">
                    <button type="button" class="tw-btn tw-btn-gray" x-on:click="open = false">Cerrar</button>
                    <button type="button" class="tw-btn tw-btn-blue" onclick="updateForecastJS()">Actualizar</button>
                </div>
            </div>
        </div>
    </div>
</div>
