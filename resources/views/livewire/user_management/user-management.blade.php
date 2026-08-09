<div>
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" class="tw-alert tw-alert-green" role="alert">
            <i class="fas fa-check-circle"></i>
            <span class="flex-1"><strong>{{ session('success') }}</strong></span>
            <button @click="show=false" class="ml-auto hover:opacity-70"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if (session('failed'))
        <div x-data="{ show: true }" x-show="show" class="tw-alert tw-alert-amber" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <span class="flex-1"><strong>{{ session('failed') }}</strong></span>
            <button @click="show=false" class="ml-auto hover:opacity-70"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" class="tw-alert tw-alert-red" role="alert">
            <i class="fas fa-times-circle"></i>
            <span class="flex-1"><strong>{{ session('error') }}</strong></span>
            <button @click="show=false" class="ml-auto hover:opacity-70"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <div class="rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-200 px-4 py-2 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <button class="tw-btn-xs tw-btn-gray" wire:click='refreshUpdateData'>
                    <i class="fas fa-sync-alt"></i>
                </button>
                <div class="text-center text-sm font-semibold text-gray-800 dark:text-gray-200">ACTUALIZAR DATOS</div>
                <div>
                    <div wire:loading><span class="tw-spinner-sm"></span></div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto p-2">
            @if($subscriptionTagE=='corporate' || $subscriptionTagOwnerE=='corporate')
                @if($user_typeOwnerE==1)
                    <form wire:submit.prevent="updateUsersSubscriptionOwner">
                        <div class="rounded border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800">
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-4">
                                <div class="mb-3">
                                    <label class="tw-label" for="business_nameOwnerE">Razón Social:</label>
                                    <input id="business_nameOwnerE" type="text" wire:model="business_nameOwnerE" class="tw-input" placeholder="" maxlength="40">
                                    @error('business_nameOwnerE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                </div>
                                <div class="mb-3">
                                    <label class="tw-label" for="nitOwnerE">Nit:</label>
                                    <input id="nitE" type="text" wire:model="nitOwnerE" class="tw-input" placeholder="" maxlength="40">
                                    @error('nitOwnerE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                </div>
                                <div class="mb-3">
                                    <label class="tw-label" for="nameOwnerE">Nombre:*</label>
                                    <input id="nameOwnerE" type="text" wire:model="nameOwnerE" class="tw-input" placeholder="" maxlength="40">
                                    @error('nameOwnerE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                </div>
                                <div class="mb-3">
                                    <label class="tw-label" for="lastnameOwnerE">Apellidos:*</label>
                                    <input id="lastnameOwnerE" type="text" wire:model="lastnameOwnerE" class="tw-input" placeholder="" maxlength="40">
                                    @error('lastnameOwnerE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-5">
                                <div class="mb-3">
                                    <label class="tw-label" for="ciOwnerE">C.I:*</label>
                                    <input id="ciOwnerE" type="text" wire:model="ciOwnerE" class="tw-input" placeholder="" maxlength="40">
                                    @error('ciOwnerE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                </div>
                                <div class="mb-3">
                                    <label class="tw-label" for="emailOwnerE">Correo:*</label>
                                    <input id="emailOwnerE" type="email" wire:model="emailOwnerE" class="tw-input" placeholder="" maxlength="100">
                                    @error('emailOwnerE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                </div>
                                <div class="mb-3">
                                    <label class="tw-label" for="number_phoneOwnerE">Nro Teléfono:*</label>
                                    <input id="number_phoneOwnerE" type="number" wire:model="number_phoneOwnerE" class="tw-input" placeholder="" maxlength="100">
                                    @error('number_phoneOwnerE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                </div>
                                <div class="mb-3">
                                    <label class="tw-label">Tipo de Suscripción:*</label>
                                    <select wire:model="subscriptionTagOwnerE" id="subscriptionTagOwnerE" name="subscriptionTagOwnerE" class="tw-input" disabled>
                                        @if(count($subscriptionsE) > 0)
                                            <option value="-1">Seleccione</option>
                                            @foreach($subscriptionsE as $item)
                                                <option value="{{$item->tag}}" @if($subscriptionTagE==$item->tag) selected @endif>{{$item->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="tw-label" for="passwordOwnerE">Nueva Contraseña:</label>
                                    <input id="passwordOwnerE" type="password" wire:model="passwordOwnerE" class="tw-input" placeholder="" maxlength="100">
                                    @error('passwordOwnerE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" class="tw-btn-xs tw-btn-gray" onclick="resetDevice({{$user_idOwnerE}})">Resetear dispositivo</button>
                            </div>
                            <div class="mt-3 flex justify-end">
                                <button class="tw-btn tw-btn-blue" type="submit">Guardar modificaciones</button>
                            </div>
                        </div>
                    </form>

                    <hr class="my-4 border-gray-200 dark:border-gray-700">

                    <div class="flex flex-col gap-4 lg:flex-row">
                        <div class="w-full lg:w-3/4">
                            <div class="mb-2 text-center text-sm font-semibold text-gray-800 dark:text-gray-200">ADMINISTRADORES DE ESTACIONES</div>
                            <div style="max-height:350px;overflow-y:auto;" class="rounded border border-gray-200 dark:border-gray-700">
                                <table class="tw-table">
                                    <thead class="themed-thead">
                                        <tr>
                                            <th style="width:5%">Nro</th>
                                            <th style="width:30%">Administrador</th>
                                            <th style="width:15%">Expiración</th>
                                            <th style="width:50%">Estación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($chooseStationListRegisteredOwner as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <div class="flex items-center justify-between gap-2">
                                                        <span>{{ strlen($item->name.$item->lastname)>0?($item->name.' '.$item->lastname):'Sin datos' }}</span>
                                                        <button type="button" wire:click='visibleUpdateManager({{$item['id']}}, {{$item['userId'] }})' class="tw-btn-xs tw-btn-gray">Ver datos</button>
                                                    </div>
                                                </td>
                                                <td>{{ $item->date_expiry }}</td>
                                                <td>
                                                    <div class="flex items-center justify-between gap-2">
                                                        <span>{{ $item->station }}</span>
                                                        <div>
                                                            @if($item->is_it_paid_renewal==1)
                                                                <div class="flex gap-1" x-data="{ chooseStationListUpdateOwnerName: @entangle('chooseStationListUpdateOwnerName') }">
                                                                    <button @click="addStationManager(@js($item))" class="tw-btn-xs tw-btn-emerald"><i class="fa fa-plus"></i></button>
                                                                    <button @click="changeStationManager(@js($item))" class="tw-btn-xs tw-btn-amber"><i class="fa fa-pencil-alt"></i></button>
                                                                    <button @click="deleteStationManager(@js($item))" class="tw-btn-xs tw-btn-red"><i class="fa fa-trash"></i></button>
                                                                </div>
                                                            @else
                                                                <span class="tw-badge tw-badge-red">Deuda pendiente</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="w-full lg:w-1/4">
                            <div class="mb-2 text-center text-sm font-semibold text-gray-800 dark:text-gray-200">AGREGAR O CAMBIAR ESTACIÓN</div>
                            <div class="mb-3">
                                <label class="tw-label" for="chooseStationListUpdateOwnerName">Elija la estación:</label>
                                <input id="chooseStationListUpdateOwnerName" type="text" list="chooseStationListUpdateOwnerL" wire:model="chooseStationListUpdateOwnerName" class="tw-input">
                                <datalist id="chooseStationListUpdateOwnerL" wire:ignore>
                                    @foreach($chooseStationListUpdateOwner as $item)
                                        <option value="{{ $item->name }}">
                                    @endforeach
                                </datalist>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4 border-gray-200 dark:border-gray-700">
                @endif

                @if($user_typeE==2)
                    <form wire:submit.prevent="updateUsersSubscription">
                        <div class="flex flex-col gap-4 lg:flex-row">
                            <div class="w-full lg:w-5/12">
                                <div class="rounded border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800">
                                    <div class="mb-3">
                                        <label class="tw-label" for="userOwnerNameAndLastNameE">Propietario:</label>
                                        <input id="userOwnerNameAndLastNameE" type="text" wire:model="userOwnerNameAndLastNameE" class="tw-input" disabled>
                                    </div>
                                    <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <div>
                                            <label class="tw-label" for="business_nameE">Razón Social:</label>
                                            <input id="business_nameE" type="text" wire:model="business_nameE" class="tw-input" placeholder="" maxlength="40" disabled>
                                            @error('business_nameE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                        </div>
                                        <div>
                                            <label class="tw-label" for="nitE">Nit:</label>
                                            <input id="nitE" type="text" wire:model="nitE" class="tw-input" placeholder="" maxlength="40" disabled>
                                            @error('nitE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                        <div>
                                            <label class="tw-label" for="nameE">Nombre:*</label>
                                            <input id="nameE" type="text" wire:model="nameE" class="tw-input" placeholder="" maxlength="40">
                                            @error('nameE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                        </div>
                                        <div>
                                            <label class="tw-label" for="lastnameE">Apellidos:*</label>
                                            <input id="lastnameE" type="text" wire:model="lastnameE" class="tw-input" placeholder="" maxlength="40">
                                            @error('lastnameE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                        </div>
                                        <div>
                                            <label class="tw-label" for="ciE">C.I:*</label>
                                            <input id="ciE" type="text" wire:model="ciE" class="tw-input" placeholder="" maxlength="40">
                                            @error('ciE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <div>
                                            <label class="tw-label" for="number_phoneE">Nro Teléfono:*</label>
                                            <input id="number_phoneE" type="number" wire:model="number_phoneE" class="tw-input" placeholder="" maxlength="100">
                                            @error('number_phoneE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                        </div>
                                        <div>
                                            <label class="tw-label">Tipo de Suscripción:*</label>
                                            <select wire:model="subscriptionTagE" id="subscriptionTagE" name="subscriptionTagE" class="tw-input" disabled>
                                                @if(count($subscriptionsE) > 0)
                                                    <option value="-1">Seleccione</option>
                                                    @foreach($subscriptionsE as $item)
                                                        <option value="{{$item->tag}}" @if($subscriptionTagE==$item->tag) selected @endif>{{$item->name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <div>
                                            <label class="tw-label" for="dateExpiryE">Expiración:</label>
                                            <input id="dateExpiryE" type="text" wire:model="dateExpiryE" class="tw-input" placeholder="" disabled>
                                        </div>
                                        @if($extensionE!==null)
                                        <div>
                                            <label class="tw-label" for="extensionE">Prórroga:</label>
                                            <input id="extensionE" type="text" wire:model="extensionE" class="tw-input" placeholder="" disabled>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="tw-label" for="emailE">Correo:*</label>
                                        <input id="emailE" type="email" wire:model="emailE" class="tw-input" placeholder="" maxlength="100">
                                        @error('emailE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="tw-label" for="passwordE">Nueva Contraseña:</label>
                                        <div class="relative">
                                            <input id="passwordE" type="password" wire:model="passwordE" class="tw-input" placeholder="" maxlength="100">
                                            <span id="toggle-passwordE" class="password-toggleE" onclick="togglePasswordVisibilityE()">&#128065;</span>
                                            @error('passwordE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="mb-3 flex flex-wrap gap-2">
                                        <button type="button" class="tw-btn-xs tw-btn-gray w-full sm:w-auto" onclick="resetDevice({{$user_idE}})">Resetear dispositivo</button>
                                        @if(auth()->user() && auth()->user()->user_type == 1)
                                            @if($stateE==1)
                                                <button type="button" class="tw-btn-xs tw-btn-red w-full sm:w-auto" onclick="changeStatusUser({{$stateE}},{{$user_idE}},'-1','manager')">Desactivar</button>
                                            @else
                                                <button type="button" class="tw-btn-xs tw-btn-emerald w-full sm:w-auto" onclick="changeStatusUser({{$stateE}},{{$user_idE}},'-1','manager')">Activar</button>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="tw-label" for="stationNameE">Estación vinculada:</label>
                                        <input id="stationNameE" type="text" wire:model="stationNameE" class="tw-input" placeholder="" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="w-full lg:w-7/12">
                                <div class="mb-2 rounded bg-gray-200 px-2 py-1 text-center text-sm font-semibold dark:bg-gray-700 dark:text-gray-200">Listado de usuarios</div>
                                <div style="height:60vh;overflow-y:auto;" class="rounded border border-gray-200 dark:border-gray-700">
                                    @php $cont=1; @endphp
                                    @foreach($elementsE as $element)
                                        <div class="border-b border-gray-100 p-2 dark:border-gray-700">
                                            <div class="flex gap-2">
                                                <div class="flex w-20 flex-shrink-0 flex-col items-center gap-1 pt-1">
                                                    <div class="text-xs text-gray-600 dark:text-gray-400">Usuario {{$cont}}:</div>
                                                    <button type="button" class="tw-btn-xs tw-btn-gray w-full text-center" onclick="resetDevice({{$element['idE']}})">Resetear disp.</button>
                                                    @if($element['stateE']==1)
                                                        <button type="button" class="tw-btn-xs tw-btn-red w-full text-center" onclick="changeStatusUser({{$element['stateE']}},{{$element['idE']}},{{$cont-1}},'delegate')">Desactivar</button>
                                                    @else
                                                        <button type="button" class="tw-btn-xs tw-btn-emerald w-full text-center" onclick="changeStatusUser({{$element['stateE']}},{{$element['idE']}},{{$cont-1}},'delegate')">Activar</button>
                                                    @endif
                                                </div>
                                                <div class="flex-1">
                                                    <div class="mb-1 grid grid-cols-2 gap-1">
                                                        <input type="text" style="width:100%;" wire:model="elementsE.{{ $loop->index }}.nameE" placeholder="Nombre" maxlength="50">
                                                        <input type="text" style="width:100%;" wire:model="elementsE.{{ $loop->index }}.mailE" placeholder="Correo electronico" maxlength="100">
                                                    </div>
                                                    <div class="mb-1 grid grid-cols-2 gap-1">
                                                        <input type="text" style="width:100%;" wire:model="elementsE.{{ $loop->index }}.lastnameE" placeholder="Apellidos" maxlength="50">
                                                        <input type="text" style="width:100%;" wire:model="elementsE.{{ $loop->index }}.phoneE" placeholder="Número teléfonico" maxlength="100">
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-1">
                                                        @if($element['is_password']=='Ya tiene contraseña')
                                                            <span class="tw-badge tw-badge-blue w-full justify-center py-1">{{$element['is_password']}}</span>
                                                        @else
                                                            <span class="tw-badge tw-badge-amber w-full justify-center py-1">{{$element['is_password']}}</span>
                                                        @endif
                                                        <input type="text" style="width:100%;" wire:model="elementsE.{{ $loop->index }}.passwordE" @if($element['is_password']=='Ya tiene contraseña') placeholder="Nueva contraseña" @else placeholder="Agregar contraseña" @endif maxlength="100">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @php $cont=$cont+1; @endphp
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 flex justify-end">
                            <button class="tw-btn tw-btn-blue" type="submit">Guardar cambios</button>
                        </div>
                    </form>

                @elseif($user_typeE==3)
                    <form wire:submit.prevent="updateUsersSubscription">
                        <div class="flex flex-col gap-4 lg:flex-row">
                            <div class="w-full lg:w-1/2">
                                <div class="rounded border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800">
                                    <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <div>
                                            <label class="tw-label" for="userOwnerNameAndLastNameE">Propietario:</label>
                                            <input id="userOwnerNameAndLastNameE" type="text" wire:model="userOwnerNameAndLastNameE" class="tw-input" disabled>
                                        </div>
                                        <div>
                                            <label class="tw-label" for="userManagerNameAndLastNameE">Administrador:</label>
                                            <input id="userManagerNameAndLastNameE" type="text" wire:model="userManagerNameAndLastNameE" class="tw-input" disabled>
                                        </div>
                                    </div>
                                    <hr class="my-3 border-gray-200 dark:border-gray-700">
                                    <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <div>
                                            <label class="tw-label" for="business_nameE">Razón Social:</label>
                                            <input id="business_nameE" type="text" wire:model="business_nameE" class="tw-input" placeholder="" maxlength="40" disabled>
                                            @error('business_nameE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                        </div>
                                        <div>
                                            <label class="tw-label" for="nitE">Nit:</label>
                                            <input id="nitE" type="text" wire:model="nitE" class="tw-input" placeholder="" maxlength="40" disabled>
                                            @error('nitE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                        <div>
                                            <label class="tw-label" for="nameE">Nombre:*</label>
                                            <input id="nameE" type="text" wire:model="nameE" class="tw-input" placeholder="" maxlength="40">
                                            @error('nameE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                        </div>
                                        <div>
                                            <label class="tw-label" for="lastnameE">Apellidos:*</label>
                                            <input id="lastnameE" type="text" wire:model="lastnameE" class="tw-input" placeholder="" maxlength="40">
                                            @error('lastnameE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                        </div>
                                        <div>
                                            <label class="tw-label" for="ciE">C.I:*</label>
                                            <input id="ciE" type="text" wire:model="ciE" class="tw-input" placeholder="" maxlength="40">
                                            @error('ciE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <div>
                                            <label class="tw-label" for="number_phoneE">Nro Teléfono:*</label>
                                            <input id="number_phoneE" type="number" wire:model="number_phoneE" class="tw-input" placeholder="" maxlength="100">
                                            @error('number_phoneE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                        </div>
                                        <div>
                                            <label class="tw-label">Tipo de Suscripción:*</label>
                                            <select wire:model="subscriptionTagE" id="subscriptionTagE" name="subscriptionTagE" class="tw-input" disabled>
                                                @if(count($subscriptionsE) > 0)
                                                    <option value="-1">Seleccione</option>
                                                    @foreach($subscriptionsE as $item)
                                                        <option value="{{$item->tag}}" @if($subscriptionTagE==$item->tag) selected @endif>{{$item->name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <div>
                                            <label class="tw-label" for="dateExpiryE">Expiración:</label>
                                            <input id="dateExpiryE" type="text" wire:model="dateExpiryE" class="tw-input" placeholder="" disabled>
                                        </div>
                                        @if($extensionE!==null)
                                        <div>
                                            <label class="tw-label" for="extensionE">Prórroga:</label>
                                            <input id="extensionE" type="text" wire:model="extensionE" class="tw-input" placeholder="" disabled>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="tw-label" for="emailE">Correo:*</label>
                                        <input id="emailE" type="email" wire:model="emailE" class="tw-input" placeholder="" maxlength="100">
                                        @error('emailE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="tw-label" for="passwordE">Nueva Contraseña:</label>
                                        <div class="relative">
                                            <input id="passwordE" type="password" wire:model="passwordE" class="tw-input" placeholder="" maxlength="100">
                                            <span id="toggle-passwordE" class="password-toggleE" onclick="togglePasswordVisibilityE()">&#128065;</span>
                                            @error('passwordE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="mb-3 flex flex-wrap gap-2">
                                        <button type="button" class="tw-btn-xs tw-btn-gray" onclick="resetDevice({{$user_idE}})">Resetear dispositivo</button>
                                        @if(auth()->user() && auth()->user()->user_type == 1)
                                            @if($stateE==1)
                                                <button type="button" class="tw-btn-xs tw-btn-red" onclick="changeStatusUser({{$stateE}},{{$user_idE}},'-1','manager')">Desactivar</button>
                                            @else
                                                <button type="button" class="tw-btn-xs tw-btn-emerald" onclick="changeStatusUser({{$stateE}},{{$user_idE}},'-1','manager')">Activar</button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-3 flex justify-end">
                                    <button class="tw-btn tw-btn-blue" type="submit">Guardar cambios</button>
                                </div>
                            </div>
                            <div wire:ignore class="w-full lg:w-1/2"></div>
                        </div>
                    </form>
                @endif

            @elseif(in_array($subscriptionTagE,['demo professional', 'professional']))
                <form wire:submit.prevent="updateUsersSubscription">
                    <div class="flex flex-col gap-4 lg:flex-row">
                        <div class="w-full lg:w-1/2">
                            <div class="rounded border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800">
                                <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="tw-label" for="business_nameE">Razón Social:</label>
                                        <input id="business_nameE" type="text" wire:model="business_nameE" class="tw-input" placeholder="" maxlength="40" @if ($subscriptionTagE=='demo professional') disabled @endif>
                                        @error('business_nameE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label class="tw-label" for="nitE">Nit:</label>
                                        <input id="nitE" type="text" wire:model="nitE" class="tw-input" placeholder="" maxlength="40" @if ($subscriptionTagE=='demo professional') disabled @endif>
                                        @error('nitE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                    <div>
                                        <label class="tw-label" for="nameE">Nombre:*</label>
                                        <input id="nameE" type="text" wire:model="nameE" class="tw-input" placeholder="" maxlength="40">
                                        @error('nameE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label class="tw-label" for="lastnameE">Apellidos:*</label>
                                        <input id="lastnameE" type="text" wire:model="lastnameE" class="tw-input" placeholder="" maxlength="40">
                                        @error('lastnameE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label class="tw-label" for="ciE">C.I:*</label>
                                        <input id="ciE" type="text" wire:model="ciE" class="tw-input" placeholder="" maxlength="40">
                                        @error('ciE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="tw-label" for="number_phoneE">Nro Teléfono:*</label>
                                        <input id="number_phoneE" type="number" wire:model="number_phoneE" class="tw-input" placeholder="" maxlength="100">
                                        @error('number_phoneE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label class="tw-label">Tipo de Suscripción:*</label>
                                        <select wire:model="subscriptionTagE" id="subscriptionTagE" name="subscriptionTagE" class="tw-input" disabled>
                                            @if(count($subscriptionsE) > 0)
                                                <option value="-1">Seleccione</option>
                                                @foreach($subscriptionsE as $item)
                                                    <option value="{{$item->tag}}" @if($subscriptionTagE==$item->tag) selected @endif>{{$item->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="tw-label" for="dateExpiryE">Expiración:</label>
                                        <input id="dateExpiryE" type="text" wire:model="dateExpiryE" class="tw-input" placeholder="" disabled>
                                    </div>
                                    @if($extensionE!==null)
                                    <div>
                                        <label class="tw-label" for="extensionE">Prórroga:</label>
                                        <input id="extensionE" type="text" wire:model="extensionE" class="tw-input" placeholder="" disabled>
                                    </div>
                                    @endif
                                </div>
                                <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="tw-label" for="emailE">Correo:*</label>
                                        <input id="emailE" type="email" wire:model="emailE" class="tw-input" placeholder="" maxlength="100">
                                        @error('emailE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label class="tw-label" for="passwordE">Nueva Contraseña:</label>
                                        <div class="relative">
                                            <input id="passwordE" type="password" wire:model="passwordE" class="tw-input" placeholder="" maxlength="100">
                                            <span id="toggle-passwordE" class="password-toggleE" onclick="togglePasswordVisibilityE()">&#128065;</span>
                                            @error('passwordE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 flex flex-wrap gap-2">
                                    <button type="button" class="tw-btn-xs tw-btn-gray" onclick="resetDevice({{$user_idE}})">Resetear dispositivo</button>
                                    @if (auth()->user()->hasAnyRole(['super_manager','manager','meteorology','meteorologist']))
                                        @if($stateE==1)
                                            <button type="button" class="tw-btn-xs tw-btn-red" onclick="changeStatusUser({{$stateE}},{{$user_idE}},'-1','manager')">Desactivar</button>
                                        @else
                                            <button type="button" class="tw-btn-xs tw-btn-emerald" onclick="changeStatusUser({{$stateE}},{{$user_idE}},'-1','manager')">Activar</button>
                                        @endif
                                    @endif
                                </div>
                                <hr class="my-3 border-gray-200 dark:border-gray-700">
                                <div class="mb-3">
                                    <label class="tw-label" for="stationNameE">Estación vinculada:</label>
                                    <input id="stationNameE" type="text" wire:model="stationNameE" class="tw-input" placeholder="" disabled>
                                </div>
                                <div class="flex justify-end">
                                    <button class="tw-btn tw-btn-blue" type="submit">Guardar cambios</button>
                                </div>
                            </div>
                        </div>

                        <div wire:ignore class="w-full lg:w-1/2">
                            @if($stationIdE === null)
                                <input id="latitudE" wire:model="latitudE" name="latitudE" type="hidden">
                                <input id="longitudE" wire:model="longitudE" name="longitudE" type="hidden">
                                <div class="rounded-t bg-gray-500 py-1 text-center text-sm text-white">Seleccione su referencia geográfica en el mapa:</div>
                                <div id="map"></div>
                            @endif
                        </div>
                    </div>
                </form>

                @if($stationIdE === null && $subscriptionTagE === 'professional')
                    <hr class="my-3 border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col gap-4 lg:flex-row">
                        <div class="w-full rounded border border-gray-200 bg-gray-100 p-3 dark:border-gray-700 dark:bg-gray-800 lg:w-1/2">
                            <p class="mb-2 text-center text-sm font-semibold text-gray-800 dark:text-gray-200">ADMINISTRAR ESTACIÓN</p>
                            <div class="flex items-end gap-2" x-data="{ stationNameNewE: @entangle('stationNameNewE') }">
                                <div class="flex-1">
                                    <label class="tw-label" for="stationNameNewE">Elija una Estación:</label>
                                    <input id="stationNameNewE" type="text" wire:model="stationNameNewE" list="listStationE" class="tw-input" placeholder="">
                                    <datalist id="listStationE">
                                        @foreach($dataStationListE as $item)
                                            <option value="{{ $item->name }}">
                                        @endforeach
                                    </datalist>
                                </div>
                                <button class="tw-btn tw-btn-amber" @click="changeStationE">Cambiar estación</button>
                            </div>
                        </div>
                    </div>
                @endif

            @elseif(in_array($subscriptionTagE,['demo personal', 'personal']))
                <form wire:submit.prevent="updateUsersSubscription">
                    <div class="flex flex-col gap-4 lg:flex-row">
                        <div class="w-full lg:w-1/2">
                            <div class="rounded border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800">
                                <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="tw-label" for="business_nameE">Razón Social:</label>
                                        <input id="business_nameE" type="text" wire:model="business_nameE" class="tw-input" placeholder="" maxlength="40" @if ($subscriptionTagE=='demo personal') disabled @endif>
                                        @error('business_nameE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label class="tw-label" for="nitE">Nit:</label>
                                        <input id="nitE" type="text" wire:model="nitE" class="tw-input" placeholder="" maxlength="40" @if ($subscriptionTagE=='demo personal') disabled @endif>
                                        @error('nitE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                    <div>
                                        <label class="tw-label" for="nameE">Nombre:*</label>
                                        <input id="nameE" type="text" wire:model="nameE" class="tw-input" placeholder="" maxlength="40">
                                        @error('nameE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label class="tw-label" for="lastnameE">Apellidos:*</label>
                                        <input id="lastnameE" type="text" wire:model="lastnameE" class="tw-input" placeholder="" maxlength="40">
                                        @error('lastnameE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label class="tw-label" for="ciE">C.I:*</label>
                                        <input id="ciE" type="text" wire:model="ciE" class="tw-input" placeholder="" maxlength="40">
                                        @error('ciE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="tw-label" for="number_phoneE">Nro Teléfono:*</label>
                                        <input id="number_phoneE" type="number" wire:model="number_phoneE" class="tw-input" placeholder="" maxlength="100">
                                        @error('number_phoneE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label class="tw-label">Tipo de Suscripción:*</label>
                                        <select wire:model="subscriptionTagE" id="subscriptionTagE" name="subscriptionTagE" class="tw-input" disabled>
                                            @if(count($subscriptionsE) > 0)
                                                <option value="-1">Seleccione</option>
                                                @foreach($subscriptionsE as $item)
                                                    <option value="{{$item->tag}}" @if($subscriptionTagE==$item->tag) selected @endif>{{$item->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="tw-label" for="dateExpiryE">Expiración:</label>
                                        <input id="dateExpiryE" type="text" wire:model="dateExpiryE" class="tw-input" placeholder="" disabled>
                                    </div>
                                    @if($extensionE!==null)
                                    <div>
                                        <label class="tw-label" for="extensionE">Prórroga:</label>
                                        <input id="extensionE" type="text" wire:model="extensionE" class="tw-input" placeholder="" disabled>
                                    </div>
                                    @endif
                                </div>
                                <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="tw-label" for="emailE">Correo:*</label>
                                        <input id="emailE" type="email" wire:model="emailE" class="tw-input" placeholder="" maxlength="100">
                                        @error('emailE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label class="tw-label" for="passwordE">Nueva Contraseña:</label>
                                        <div class="relative">
                                            <input id="passwordE" type="password" wire:model="passwordE" class="tw-input" placeholder="" maxlength="100">
                                            <span id="toggle-passwordE" class="password-toggleE" onclick="togglePasswordVisibilityE()">&#128065;</span>
                                            @error('passwordE')<span class="text-xs text-blue-600">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 flex flex-wrap gap-2">
                                    <button type="button" class="tw-btn-xs tw-btn-gray" onclick="resetDevice({{$user_idE}})">Resetear dispositivo</button>
                                    @if (auth()->user()->hasAnyRole(['super_manager','manager','meteorology','meteorologist']))
                                        @if($stateE==1)
                                            <button type="button" class="tw-btn-xs tw-btn-red" onclick="changeStatusUser({{$stateE}},{{$user_idE}},'-1','manager')">Desactivar</button>
                                        @else
                                            <button type="button" class="tw-btn-xs tw-btn-emerald" onclick="changeStatusUser({{$stateE}},{{$user_idE}},'-1','manager')">Activar</button>
                                        @endif
                                    @endif
                                </div>
                                <hr class="my-3 border-gray-200 dark:border-gray-700">
                                <div class="flex justify-end">
                                    <button class="tw-btn tw-btn-blue" type="submit">Guardar cambios</button>
                                </div>
                            </div>
                        </div>
                        <div wire:ignore class="w-full lg:w-1/2">
                            <input id="latitudE" wire:model="latitudE" name="latitudE" type="hidden">
                            <input id="longitudE" wire:model="longitudE" name="longitudE" type="hidden">
                            <div class="rounded-t bg-gray-500 py-1 text-center text-sm text-white">Seleccione su referencia geográfica en el mapa:</div>
                            <div wire:ignore id="map" style="height: 70vh"></div>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
