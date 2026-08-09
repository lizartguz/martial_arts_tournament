<div>
    <div><h5><span class="tw-badge tw-badge-gray text-sm px-3 py-1">{{$namefullUser ?? ''}}</span></h5></div>
    <div class="rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex flex-wrap items-center gap-4">
                    @if($selectionType=="renewal_station")
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 dark:text-gray-300">
                            <input class="accent-emerald-600" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="renewal_station" wire:model="selectedOption" wire:click="getData" wire:loading.attr="disabled">
                            Renovación de estaciones
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 dark:text-gray-300">
                            <input class="accent-emerald-600" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="new_user" wire:model="selectedOption" wire:click="getData" wire:loading.attr="disabled">
                            Nuevo usuario de estación
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 dark:text-gray-300">
                            <input class="accent-emerald-600" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="new_station" wire:model="selectedOption" wire:click="getData" wire:loading.attr="disabled">
                            Nueva estación
                        </label>
                    @elseif($selectionType=="renewal_professional")
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 dark:text-gray-300">
                            <input class="accent-emerald-600" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="renewal_station" wire:model="selectedOption">
                            Renovación
                        </label>
                    @elseif($selectionType=="renewal_personal")
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 dark:text-gray-300">
                            <input class="accent-emerald-600" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="renewal_personal" wire:model="selectedOption">
                            Renovación
                        </label>
                    @endif
                    <div wire:loading class="flex items-center gap-1">
                        <span class="inline-block h-2 w-2 animate-bounce rounded-full bg-gray-500" style="animation-delay:0s"></span>
                        <span class="inline-block h-2 w-2 animate-bounce rounded-full bg-gray-500" style="animation-delay:.15s"></span>
                        <span class="inline-block h-2 w-2 animate-bounce rounded-full bg-gray-500" style="animation-delay:.3s"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($selectedOption=="renewal_station")
    <div class="flex flex-col gap-4 lg:flex-row">
        <div class="w-full lg:w-2/3">
            <div class="tw-table-wrap">
                <table class="tw-table">
                    <thead class="themed-thead">
                        <tr>
                            <th>Nro</th>
                            <th>Estación</th>
                            <th>Expiración</th>
                            <th>Prórroga</th>
                            <th>Cantidad (Años)</th>
                            <th>Fecha actualizada</th>
                            <th>Costo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($listItemSubscript))
                            @for ($i = 0; $i < count($listItemSubscript); $i++)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $listItemSubscript[$i]['station_name'] }}</td>
                                <td>{{ $listItemSubscript[$i]['date_expiry'] }}</td>
                                <td>{{ $listItemSubscript[$i]['temporary_extension_end'] }}</td>
                                @if($listItemSubscript[$i]['is_it_paid_renewal'])
                                <td>
                                    <select wire:model="selectAmount.{{ $listItemSubscript[$i]['id'] }}" class="tw-input" wire:change="applyQuantity({{$listItemSubscript[$i]['id']}},{{$i}})" wire:loading.attr="disabled">
                                        <option value="-1">Seleccione</option>
                                        @foreach([1,2,3,4,5] as $n)<option value="{{ $n }}">{{ $n }}</option>@endforeach
                                    </select>
                                </td>
                                @else
                                <td>
                                    <a class="tw-btn-xs tw-btn-amber w-full" href="{{$listItemSubscript[$i]['payment_link_renewal']}}">Deuda pendiente</a>
                                </td>
                                @endif
                                <td><span class="text-emerald-600">{{ $selectDateAndCash[$listItemSubscript[$i]['id']][0] }}</span></td>
                                <td><span class="text-blue-600">{{ $selectDateAndCash[$listItemSubscript[$i]['id']][1] }} Bs</span></td>
                            </tr>
                            @endfor
                            <tr>
                                <td colspan="6" class="text-right font-semibold">Total:</td>
                                <td>
                                    <div wire:loading wire:target="applyQuantity" class="text-xs text-gray-500"><i class="fas fa-spinner fa-spin"></i> Calculando...</div>
                                    <div wire:loading.remove wire:target="applyQuantity"><strong>{{ number_format($total_to_pay, 2) }} Bs</strong></div>
                                </td>
                            </tr>
                        @else
                            <tr><td colspan="7" class="py-4 text-center text-gray-500">No hay registros</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="w-full lg:w-1/3">
            @if($enablePayment)
            <div class="mb-3">
                <label class="tw-label" for="payType">Formas de pago:*</label>
                <div class="flex items-center gap-2">
                    <select wire:model="payType" wire:change='payWith' class="tw-input" wire:loading.attr="disabled">
                        <option value="-1">Seleccione</option>
                        <option value="QR/Tarjeta">QR / Tarjeta</option>
                        @if (!auth()->user()->hasAnyRole(['subscriber','sales','marketing','technical']))
                        <option value="Efectivo">Efectivo</option>
                        <option value="Cortesía">Cortesía</option>
                        @endif
                    </select>
                    <div wire:loading><span class="tw-spinner-sm"></span></div>
                </div>
            </div>
            @endif
            <div class="rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="rounded-t-lg bg-gray-500 px-4 py-2 text-sm font-medium text-white">Duración y Costo</div>
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    <li class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">Vencimiento: {{$dateNewDateStation}}</li>
                    <li class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">Total: {{$total_to_pay}} Bs</li>
                </ul>
            </div>
            @if($linkDePago!=null)
            <div class="mt-3">
                <a class="tw-btn tw-btn-blue w-full justify-center" href="{{ $linkDePago }}" target="_blank">Ir a Pagar</a>
            </div>
            @endif
        </div>
    </div>

    @elseif($selectedOption=="new_user")
    <div class="flex flex-col gap-4 lg:flex-row">
        <div class="w-full lg:w-2/3">
            <div class="tw-table-wrap">
                <table class="tw-table">
                    <thead class="themed-thead">
                        <tr>
                            <th>Nro</th>
                            <th>Estación</th>
                            <th>Expiración</th>
                            <th>Prórroga</th>
                            <th>Cantidad (Usuarios)</th>
                            <th>Costo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($listItemSubscript))
                            @for ($i = 0; $i < count($listItemSubscript); $i++)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $listItemSubscript[$i]['station_name'] }}</td>
                                <td>{{ $listItemSubscript[$i]['date_expiry'] }}</td>
                                <td>{{ $listItemSubscript[$i]['temporary_extension_end'] }}</td>
                                @if($listItemSubscript[$i]['is_it_paid_user']==1)
                                <td>
                                    <select wire:model="selectAmount.{{ $listItemSubscript[$i]['id'] }}" class="tw-input" wire:change="applyQuantity({{$listItemSubscript[$i]['id']}},{{$i}})" wire:loading.attr="disabled">
                                        <option value="-1">Seleccione</option>
                                        @foreach([1,2,3,4,5] as $n)<option value="{{ $n }}">{{ $n }}</option>@endforeach
                                    </select>
                                </td>
                                @else
                                <td>
                                    <a class="tw-btn-xs tw-btn-amber w-full" href="{{$listItemSubscript[$i]['payment_link_user']}}">Deuda pendiente</a>
                                </td>
                                @endif
                                <td><span class="text-blue-600">{{ $selectDateAndCash[$listItemSubscript[$i]['id']][1] }} Bs</span></td>
                            </tr>
                            @endfor
                            <tr>
                                <td colspan="5" class="text-right font-semibold">Total:</td>
                                <td>
                                    <div wire:loading wire:target="applyQuantity" class="text-xs text-gray-500"><i class="fas fa-spinner fa-spin"></i> Calculando...</div>
                                    <div wire:loading.remove wire:target="applyQuantity"><strong>{{ number_format($total_to_pay, 2) }} Bs</strong></div>
                                </td>
                            </tr>
                        @else
                            <tr><td colspan="7" class="py-4 text-center text-gray-500">No hay registros</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="w-full lg:w-1/3">
            @if($enablePayment)
            <div class="mb-3">
                <label class="tw-label" for="payType">Formas de pago:*</label>
                <div class="flex items-center gap-2">
                    <select wire:model="payType" wire:change='payWith' class="tw-input" wire:loading.attr="disabled">
                        <option value="-1">Seleccione</option>
                        <option value="QR/Tarjeta">QR / Tarjeta</option>
                        @if (!auth()->user()->hasAnyRole(['subscriber','sales','marketing','technical']))
                        <option value="Efectivo">Efectivo</option>
                        <option value="Cortesía">Cortesía</option>
                        @endif
                    </select>
                    <div wire:loading><span class="tw-spinner-sm"></span></div>
                </div>
            </div>
            @endif
            @if($linkDePago!=null)
            <div class="mt-3">
                <a class="tw-btn tw-btn-blue w-full justify-center" href="{{ $linkDePago }}" target="_blank">Ir a Pagar</a>
            </div>
            @endif
        </div>
    </div>

    @elseif($selectedOption=="new_station")
    <form wire:submit.prevent="addPayNewStation">
        <div class="flex flex-col gap-4 lg:flex-row">
            <div class="w-full lg:w-3/4">
                <div class="rounded border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800">
                    <div class="mb-3 text-center">
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Datos del nuevo administrador para la nueva estación</p>
                    </div>
                    <div class="mb-3 grid grid-cols-1 gap-3 md:grid-cols-3">
                        <div>
                            <label class="tw-label" for="name">Nombre:*</label>
                            <input id="name" type="text" wire:model="name" class="tw-input" placeholder="" maxlength="40" @if($enablePayment) disabled @endif>
                            @error('name')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="tw-label" for="lastname">Apellidos:*</label>
                            <input id="lastname" type="text" wire:model="lastname" class="tw-input" placeholder="" maxlength="40" @if($enablePayment) disabled @endif>
                            @error('lastname')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="tw-label" for="email">Correo:*</label>
                            <input id="email" type="email" wire:model="email" class="tw-input" placeholder="" maxlength="100" @if($enablePayment) disabled @endif>
                            @error('email')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="mb-3 grid grid-cols-1 gap-3 md:grid-cols-3">
                        <div>
                            <label class="tw-label" for="number_phone">Nro Teléfono:*</label>
                            <input id="number_phone" type="number" wire:model="number_phone" class="tw-input" placeholder="" maxlength="100" @if($enablePayment) disabled @endif>
                            @error('number_phone')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="tw-label" for="password">Contraseña:*</label>
                            <input id="password" type="password" wire:model="password" class="tw-input" placeholder="" maxlength="100" @if($enablePayment) disabled @endif>
                            @error('password')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="tw-label" for="password">Tiempo de vigencia:*</label>
                            <select wire:model="valueDuration" class="tw-input" wire:change="applyQuantity()" wire:loading.attr="disabled" @if($enablePayment) disabled @endif>
                                <option value="-1">Seleccione</option>
                                @foreach([1=>1,2=>2,3=>3,4=>4,5=>5] as $n=>$v)
                                <option value="{{ $n }}">{{ $n }} {{ $n==1?'Año':'Años' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 grid grid-cols-1 gap-3 md:grid-cols-3">
                        <div>
                            <label class="tw-label">Estación</label>
                            <select wire:model="selectedStationId" class="tw-input" wire:loading.attr="disabled" @if($enablePayment) disabled @endif>
                                <option value="-1">Seleccione</option>
                                @foreach ($stationList as $station)
                                    <option value="{{ $station['id'] }}">{{$station['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div></div>
                        <div class="flex items-end">
                            @if($enablePayment)
                                <button class="tw-btn tw-btn-gray w-full" wire:click='fromBack' type="button">Volver a editar el formulario</button>
                            @else
                                <button class="tw-btn tw-btn-emerald w-full" type="submit">Guardar y continuar</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full lg:w-1/4">
                @if($enablePayment)
                <div class="mb-3">
                    <label class="tw-label" for="payType">Formas de pago:*</label>
                    <select wire:model="payType" wire:change='payWith' class="tw-input" wire:loading.attr="disabled">
                        <option value="-1">Seleccione</option>
                        <option value="QR/Tarjeta">QR / Tarjeta</option>
                        @if (!auth()->user()->hasAnyRole(['subscriber','sales','marketing','technical']))
                        <option value="Efectivo">Efectivo</option>
                        <option value="Cortesía">Cortesía</option>
                        @endif
                    </select>
                </div>
                @endif
                <div class="rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="rounded-t-lg bg-gray-500 px-4 py-2 text-sm font-medium text-white">Duración y Costo</div>
                    <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                        <li class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">Vencimiento: {{$dateNewDateStation}}</li>
                        <li class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">Total: {{$total_to_pay}} Bs</li>
                    </ul>
                </div>
                @if($linkDePago!=null)
                <div class="mt-3">
                    <a class="tw-btn tw-btn-blue w-full justify-center" href="{{ $linkDePago }}" target="_blank">Ir a Pagar</a>
                </div>
                @endif
            </div>
        </div>
    </form>

    @elseif($selectedOption=="renewal_personal")
        @if($isItPaidRenewal==1)
        <form wire:submit.prevent="addPayNewPersonal">
            <div class="flex flex-col gap-4 lg:flex-row">
                <div class="w-full lg:w-3/4">
                    <div class="rounded border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800">
                        <div class="mb-3 text-center">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Renovación</p>
                        </div>
                        <div class="mb-3 grid grid-cols-1 gap-3 md:grid-cols-2">
                            <div>
                                <label class="tw-label" for="business_name">Razón Social:*</label>
                                <input id="business_name" type="text" wire:model="business_name" class="tw-input" placeholder="" maxlength="40" @if($enablePayment) disabled @endif>
                                @error('business_name')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label class="tw-label" for="nit">Nit:*</label>
                                <input id="nit" type="text" wire:model="nit" class="tw-input" placeholder="" maxlength="40" @if($enablePayment) disabled @endif>
                                @error('nit')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="mb-3 grid grid-cols-1 gap-3 md:grid-cols-3">
                            <div>
                                <label class="tw-label" for="expiration_date">Expiración:*</label>
                                <input id="expiration_date" type="text" value="{{$expiration_date}}" class="tw-input" placeholder="" maxlength="100" disabled>
                            </div>
                            <div>
                                <label class="tw-label" for="extension">Prórroga:*</label>
                                <input id="extension" type="text" value="{{$extension}}" class="tw-input" placeholder="" maxlength="100" disabled>
                            </div>
                            <div>
                                <label class="tw-label">Tiempo de vigencia:*</label>
                                <select wire:model="valueDuration" class="tw-input" wire:change="applyQuantity()" wire:loading.attr="disabled" @if($enablePayment) disabled @endif>
                                    <option value="-1">Seleccione</option>
                                    @foreach([1=>1,2=>2,3=>3,4=>4,5=>5] as $n=>$v)
                                    <option value="{{ $n }}">{{ $n }} {{ $n==1?'Año':'Años' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            @if($enablePayment)
                                <button class="tw-btn tw-btn-gray" wire:click='fromBack' type="button">Volver a editar el formulario</button>
                            @else
                                <button class="tw-btn tw-btn-emerald" type="submit">Guardar y continuar</button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="w-full lg:w-1/4">
                    @if($enablePayment)
                    <div class="mb-3">
                        <label class="tw-label" for="payType">Procesar con pago:*</label>
                        <div class="flex items-center gap-2">
                            <select wire:model="payType" wire:change='payWith' class="tw-input" wire:loading.attr="disabled">
                                <option value="-1">Seleccione</option>
                                <option value="QR/Tarjeta">QR / Tarjeta</option>
                                @if (!auth()->user()->hasAnyRole(['subscriber','sales','marketing','technical']))
                                <option value="Efectivo">Efectivo</option>
                                <option value="Cortesía">Cortesía</option>
                                @endif
                            </select>
                            <div wire:loading><span class="tw-spinner-sm"></span></div>
                        </div>
                    </div>
                    @endif
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="rounded-t-lg bg-gray-500 px-4 py-2 text-sm font-medium text-white">Duración y Costo</div>
                        <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                            <li class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">Vencimiento: {{$dateNewDateStation}}</li>
                            <li class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">Total: {{$total_to_pay}} Bs</li>
                        </ul>
                    </div>
                    @if($linkDePago!=null)
                    <div class="mt-3">
                        <a class="tw-btn tw-btn-blue w-full justify-center" href="{{ $linkDePago }}" target="_blank">Ir a Pagar</a>
                    </div>
                    @endif
                </div>
            </div>
        </form>
        @else
        <div class="mt-4">
            <a class="tw-btn tw-btn-amber" href="{{$linkPayPersonal}}">Deuda pendiente</a>
        </div>
        @endif
    @endif
</div>
