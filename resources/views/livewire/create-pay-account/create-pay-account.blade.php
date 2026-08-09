<div>
    <div class="flex flex-col gap-4 lg:flex-row">
        {{-- Columna principal: formulario --}}
        <div class="w-full px-4 py-3 lg:w-3/4">
            <form wire:submit.prevent="addPayNewAccount">
                <h5 class="my-4 text-center font-semibold text-gray-800 dark:text-gray-200">Crea una cuenta</h5>

                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                    <div class="mb-3 flex justify-center">
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300">INGRESE SUS DATOS</p>
                    </div>

                    <div class="hidden">
                        <input type="text" name="hp_field" id="hp_field">
                    </div>
                    <input type="hidden" name="form_loaded_at" value="{{ time() }}">

                    <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label class="tw-label" for="business_name">Razón social:</label>
                            <input id="business_name" type="text" wire:model="business_name" class="tw-input" placeholder="" maxlength="40" @if($enablePayment) disabled @endif>
                            @error('business_name')
                                <span class="text-xs text-blue-600">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="tw-label" for="nit">Nit/CI:</label>
                            <input id="nit" type="number" wire:model="nit" class="tw-input" placeholder="" maxlength="40" @if($enablePayment) disabled @endif>
                            @error('nit')
                                <span class="text-xs text-blue-600">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="tw-label" for="occupation">Ocupación:*</label>
                            <select wire:model="occupation" class="tw-input" wire:loading.attr="disabled" @if($enablePayment) disabled @endif>
                                <option value="-1">Seleccione</option>
                                @for($i = 0; $i < count($occupations); $i++)
                                    <option value="{{ $occupations[$i] }}">{{ $occupations[$i] }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label class="tw-label" for="name">Nombre:*</label>
                            <input id="name" type="text" wire:model="name" class="tw-input" placeholder="" maxlength="40" @if($enablePayment) disabled @endif>
                            @error('name')
                                <span class="text-xs text-blue-600">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="tw-label" for="lastname">Apellidos:*</label>
                            <input id="lastname" type="text" wire:model="lastname" class="tw-input" placeholder="" maxlength="40" @if($enablePayment) disabled @endif>
                            @error('lastname')
                                <span class="text-xs text-blue-600">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="tw-label" for="email">Correo:*</label>
                            <input id="email" type="email" wire:model="email" class="tw-input" placeholder="" maxlength="100" @if($enablePayment) disabled @endif>
                            @error('email')
                                <span class="text-xs text-blue-600">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label class="tw-label" for="number_phone">Nro Teléfono:</label>
                            <input id="number_phone" type="number" wire:model="number_phone" class="tw-input" placeholder="" maxlength="100" @if($enablePayment) disabled @endif>
                            @error('number_phone')
                                <span class="text-xs text-blue-600">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="tw-label" for="password">Contraseña:*</label>
                            <input id="password" type="password" wire:model="password" class="tw-input" placeholder="" maxlength="100" @if($enablePayment) disabled @endif>
                            @error('password')
                                <span class="text-xs text-blue-600">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="tw-label" for="valueTypeSubs">Tipo de suscripción:*</label>
                            <select wire:model="valueTypeSubs" class="tw-input" wire:change="applyType" wire:loading.attr="disabled" @if($enablePayment) disabled @endif>
                                <option value="-1">Seleccione</option>
                                @for($i = 0; $i < count($typeSubsArray); $i++)
                                    <option value="{{ $typeSubsArray[$i] }}">{{ $typeSubsNameArray[$i] }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    @if($formCorporate)
                        <hr class="my-4 border-gray-200 dark:border-gray-700">
                        <div class="mb-3 flex justify-center">
                            <p class="text-sm font-bold text-gray-700 dark:text-gray-300">DATOS PARA EL ADMINISTRADOR DE ESTACIÓN</p>
                        </div>
                        <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label class="tw-label" for="nameAdm">Nombre:*</label>
                                <input id="nameAdm" type="text" wire:model="nameAdm" class="tw-input" placeholder="" maxlength="40" @if($enablePayment) disabled @endif>
                                @error('nameAdm')
                                    <span class="text-xs text-blue-600">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="tw-label" for="lastnameAdm">Apellidos:*</label>
                                <input id="lastnameAdm" type="text" wire:model="lastnameAdm" class="tw-input" placeholder="" maxlength="40" @if($enablePayment) disabled @endif>
                                @error('lastnameAdm')
                                    <span class="text-xs text-blue-600">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="tw-label" for="emailAdm">Correo:*</label>
                                <input id="emailAdm" type="email" wire:model="emailAdm" class="tw-input" placeholder="" maxlength="100" @if($enablePayment) disabled @endif>
                                @error('emailAdm')
                                    <span class="text-xs text-blue-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label class="tw-label" for="number_phoneAdm">Nro Teléfono:</label>
                                <input id="number_phoneAdm" type="number" wire:model="number_phoneAdm" class="tw-input" placeholder="" maxlength="100" @if($enablePayment) disabled @endif>
                                @error('number_phoneAdm')
                                    <span class="text-xs text-blue-600">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="tw-label" for="passwordAdm">Contraseña:*</label>
                                <input id="passwordAdm" type="password" wire:model="passwordAdm" class="tw-input" placeholder="" maxlength="100" @if($enablePayment) disabled @endif>
                                @error('passwordAdm')
                                    <span class="text-xs text-blue-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    @endif

                    @if($buttonSubscript && !$enablePayment)
                        <div class="mt-4 flex justify-end">
                            <button class="tw-btn tw-btn-emerald" type="submit">Suscribirse</button>
                        </div>
                    @endif
                </div>
            </form>
        </div>

        {{-- Columna lateral: pago --}}
        <div class="mt-4 w-full lg:w-1/4">
            @if($enablePayment)
                <div class="mb-4">
                    <label class="tw-label" for="payType">Formas de pago:*</label>
                    <div class="tw-alert tw-alert-amber text-xs">
                        Una vez seleccionada la <strong>forma de pago</strong>, inmediatamente se procesará el registro, si su selección fué: <strong>Efectivo o cortesia.</strong><br>
                        Pero si seleccionó: <strong>QR/Tarjeta</strong>, se generará un botón con el enlace para completar el pago.
                    </div>
                    <div class="flex items-center gap-2">
                        <select wire:model="payType" wire:change='payWith' class="tw-input" wire:loading.attr="disabled">
                            <option value="-1">Seleccione</option>
                            <option value="QR/Tarjeta">QR / Tarjeta</option>
                            @if (!auth()->user()->hasAnyRole(['subscriber','sales','marketing','technical']))
                            <option value="Efectivo">Efectivo</option>
                            <option value="Cortesía">Cortesía</option>
                            @endif
                        </select>
                        <div wire:loading class="ml-2 mt-1"><span class="tw-spinner-sm"></span></div>
                    </div>
                </div>
            @endif

            <div class="rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="rounded-t-lg bg-gray-500 px-4 py-2 text-sm font-medium text-white">Duración y Costo</div>
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @if($valueTypeSubs == "demo personal" || $valueTypeSubs == "demo professional")
                        <li class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">Vencimiento: {{ $dateRegister ?? '' }}</li>
                        <li class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                            Total: {{ $total_to_pay ?? '' }} Bs <span class="font-bold text-emerald-600">(Gratuito por 7 días)</span>
                        </li>
                    @else
                        <li class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">Vencimiento: {{ $dateRegister }}</li>
                        <li class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">Total: {{ $total_to_pay }} Bs</li>
                    @endif
                </ul>
            </div>

            @if($linkDePago != null)
                <div class="mt-3">
                    <a class="tw-btn tw-btn-blue w-full justify-center" href="{{ $linkDePago ?? '' }}" target="_blank">Ir a Pagar</a>
                </div>
            @endif
        </div>
    </div>

    {{-- Toast de éxito (Tailwind/Alpine) --}}
    <div x-data="{ show: false }"
         x-on:open-success-toast.window="show = true"
         x-show="show" x-cloak
         class="fixed bottom-0 right-0 z-[60] p-3">
        <div id="successToast" class="flex items-start gap-2 rounded-md bg-emerald-600 text-white shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="p-3 text-sm">
                ¡Registro completado con éxito!<br>
                Por favor, accede a la aplicación móvil SenvaTec 3.0. Puedes iniciar sesión utilizando tu correo electrónico o número de teléfono, junto con la contraseña que registraste al crear tu cuenta en este formulario.
            </div>
            <button type="button" class="m-auto mr-2 p-1 text-white/80 hover:text-white" x-on:click="show = false" aria-label="Close"><i class="fas fa-times"></i></button>
        </div>
    </div>
</div>

@section('js')
<script>
    Livewire.on('openLink', (event) => {
        window.open(event[0].link, '_blank');
    });
</script>
@stop
