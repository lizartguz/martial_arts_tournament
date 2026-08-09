<div>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

        {{-- Columna 1: Lista de usuarios (origen) --}}
        <div>
            <div class="mb-2">
                <label for="searchUser" class="tw-label">Buscar por:</label>
                <input type="text" id="searchUser" wire:model.live="searchUser"
                       class="tw-input-sm" placeholder="Nombre completo o Correo"
                       @if($formAdd) disabled @endif>
            </div>
            <div class="tw-table-wrap">
                <table class="tw-table">
                    <thead class="themed-thead">
                        <tr>
                            @if (auth()->user()->hasAnyRole(['super_manager']))
                                <th>ID</th>
                            @endif
                            <th>Usuario</th>
                            <th class="text-center">Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($data) > 0)
                            @for ($i = 0; $i < count($data); $i++)
                                <tr>
                                    @if (auth()->user()->hasAnyRole(['super_manager']))
                                        <td>{{ $data[$i]->user->id }}</td>
                                    @endif
                                    <td>
                                        <div class="font-medium text-gray-800 dark:text-gray-200">{{ $data[$i]->user->name }} {{ $data[$i]->user->lastname }}</div>
                                        <div class="rounded px-1 text-[9pt]" style="background-color:rgb(228,226,98);color:black;">{{ $data[$i]->user->email ?? 'Sin correo' }}</div>
                                        <div class="rounded px-1 text-[9pt] text-white" style="background-color:#082C5F;">{{ $data[$i]->subscription->subscription->name }}</div>
                                        <div class="rounded px-1 text-[9pt]" style="background-color:#CECBCB;">
                                            {{ $data[$i]->user->user_type !== null ? ($data[$i]->user->user_type == 1 ? 'Propietario' : ($data[$i]->user->user_type == 2 ? 'Administrador' : 'Dependiente')) : '' }}
                                        </div>
                                        @if($data[$i]->subscription->station !== null)
                                            <div class="rounded px-1 text-[9pt]" style="background-color:#EBEAEA;color:black;">{{ $data[$i]->subscription->station->name }}</div>
                                        @else
                                            <div class="rounded px-1 text-[9pt]" style="background-color:rgb(37,214,214);color:black;">Sin Estación</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <label class="flex cursor-pointer items-center justify-center gap-1 text-sm">
                                            <input type="radio" name="radio-stacked"
                                                   id="{{ 'customControlValidation' . $data[$i]->user->id }}"
                                                   wire:model="selectedUserId"
                                                   value="{{ $data[$i]->user->id }}"
                                                   class="accent-emerald-600" required>
                                            Seleccionar
                                        </label>
                                    </td>
                                </tr>
                            @endfor
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Columna 2: Opciones de rol y suscripción --}}
        <div class="space-y-4">
            <div class="tw-table-wrap">
                <table class="tw-table">
                    <thead class="themed-thead">
                        <tr><th>TIPO DE USUARIO</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="space-y-2">
                                    @foreach([['propietario','Propietario'],['administrador','Administrador'],['dependiente','Dependiente']] as [$val,$label])
                                        <label class="flex cursor-pointer items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                            <input type="radio" name="rol" value="{{ $val }}" wire:model="rolSeleccionado" class="accent-emerald-600">
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="tw-table-wrap">
                <table class="tw-table">
                    <thead class="themed-thead">
                        <tr><th class="text-center">TIPO DE SUSCRIPCIÓN</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="space-y-1.5">
                                    @foreach([['demo','Demo'],['personal','Personal'],['demo_pro','Demo Profesional'],['profesional','Profesional'],['corporativo','Corporativo']] as [$val,$label])
                                        <label class="flex cursor-pointer items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                            <input type="radio" id="{{ $val }}" name="suscripcion" value="{{ $val }}" wire:model="tipoSuscripcion" class="accent-emerald-600">
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center gap-2">
                <div wire:loading wire:target="eliminarI,changeStatus,deleteNotif,visibleAdd,noVisibleAdd">
                    <span class="tw-spinner"></span>
                </div>
                <button class="tw-btn tw-btn-emerald w-full" wire:click='visibleAdd'>Aplicar</button>
            </div>
        </div>

        {{-- Columna 3: Lista de usuarios (destino) --}}
        <div>
            <div class="mb-2">
                <label for="searchUserEnd" class="tw-label">Buscar por:</label>
                <input type="text" id="searchUserEnd" wire:model.live="searchUserEnd"
                       class="tw-input-sm" placeholder="Nombre completo o Correo"
                       @if($formAdd) disabled @endif>
            </div>
            <div class="tw-table-wrap">
                <table class="tw-table">
                    <thead class="themed-thead">
                        <tr>
                            @if (auth()->user()->hasAnyRole(['super_manager']))
                                <th>ID</th>
                            @endif
                            <th>Usuario</th>
                            <th class="text-center">Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($data) > 0)
                            @for ($i = 0; $i < count($data); $i++)
                                <tr>
                                    @if (auth()->user()->hasAnyRole(['super_manager']))
                                        <td>{{ $data[$i]->user->id }}</td>
                                    @endif
                                    <td>
                                        <div class="font-medium text-gray-800 dark:text-gray-200">{{ $data[$i]->user->name }} {{ $data[$i]->user->lastname }}</div>
                                        <div class="rounded px-1 text-[9pt]" style="background-color:rgb(228,226,98);color:black;">{{ $data[$i]->user->email ?? 'Sin correo' }}</div>
                                        <div class="rounded px-1 text-[9pt] text-white" style="background-color:#082C5F;">{{ $data[$i]->subscription->subscription->name }}</div>
                                        <div class="rounded px-1 text-[9pt]" style="background-color:#CECBCB;">
                                            {{ $data[$i]->user->user_type !== null ? ($data[$i]->user->user_type == 1 ? 'Propietario' : ($data[$i]->user->user_type == 2 ? 'Administrador' : 'Dependiente')) : '' }}
                                        </div>
                                        @if($data[$i]->subscription->station !== null)
                                            <div class="rounded px-1 text-[9pt]" style="background-color:#EBEAEA;color:black;">{{ $data[$i]->subscription->station->name }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <label class="flex cursor-pointer items-center justify-center gap-1 text-sm">
                                            <input type="radio" name="radio-stacked"
                                                   id="{{ 'customControlValidation_end' . $data[$i]->user->id }}"
                                                   wire:model="selectedUserIdEnd"
                                                   value="{{ $data[$i]->user->id }}"
                                                   class="accent-emerald-600" required>
                                            Seleccionar
                                        </label>
                                    </td>
                                </tr>
                            @endfor
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
