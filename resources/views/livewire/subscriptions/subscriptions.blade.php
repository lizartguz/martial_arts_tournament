<div>
    @if (session('message'))
        <div x-data="{ show: true }" x-show="show" class="tw-alert tw-alert-green" role="alert">
            <i class="fas fa-check-circle"></i>
            <span class="flex-1"><strong>{{ session('message') }}</strong></span>
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
        {{-- Header --}}
        <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
            @if($formAdd)
                <div class="mb-2 text-center">
                    <h5 class="font-semibold text-gray-800 dark:text-gray-200">{{ __('messages.subscription_types.create_title') }}</h5>
                </div>
            @endif
            @if($formUpdate)
                <div class="mb-2 text-center">
                    <h5 class="font-semibold text-gray-800 dark:text-gray-200">{{ __('messages.subscription_types.edit_title') }}</h5>
                </div>
            @endif
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex flex-wrap gap-3">
                    @if($formAdd==false && $formUpdate==false)
                        <div>
                            <label class="tw-label">{{ __('messages.subscription_types.filters.pagination') }}</label>
                            <select wire:model.live="perPage" class="tw-input" style="width:auto">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <div>
                            <label class="tw-label">{{ __('messages.subscription_types.filters.status') }}</label>
                            <select wire:model.live="stateChange" class="tw-input" style="width:auto">
                                <option value="all">{{ __('messages.subscription_types.filters.all') }}</option>
                                <option value="1">{{ __('messages.subscription_types.status.active') }}</option>
                                <option value="0">{{ __('messages.subscription_types.status.inactive') }}</option>
                            </select>
                        </div>
                    @endif
                </div>
                <div class="ml-auto">
                    @if($formAdd)
                        <button class="tw-btn tw-btn-gray" wire:click='visibleAdd'>{{ __('messages.subscription_types.buttons.exit_form') }}</button>
                    @elseif($formUpdate)
                        <button class="tw-btn tw-btn-gray" wire:click='closedUpdate'>{{ __('messages.subscription_types.buttons.exit_form') }}</button>
                    @else
                        @if(auth()->user()->hasAnyRole(['super_manager']))
                            <button class="tw-btn tw-btn-emerald" wire:click='visibleAdd'><i class="fas fa-plus mr-1"></i>{{ __('messages.subscription_types.buttons.add') }}</button>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div>
            @if($formAdd==false && $formUpdate==false)
                <div class="tw-table-wrap">
                    <table class="tw-table">
                        <thead class="themed-thead">
                            <tr>
                                <th>{{ __('messages.subscription_types.table.subscription') }}</th>
                                <th>{{ __('messages.subscription_types.table.duration') }}</th>
                                <th class="text-center">{{ __('messages.subscription_types.table.discount_by_duration') }}</th>
                                <th class="text-center">{{ __('messages.subscription_types.table.discount_by_stations') }}</th>
                                <th>{{ __('messages.subscription_types.table.status') }}</th>
                                <th class="text-center">{{ __('messages.subscription_types.table.options') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($data))
                                @foreach ($data as $item)
                                    <tr>
                                        <td>
                                            <div class="flex items-center text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $item->name }}</div>
                                            <ul class="mt-1 list-none space-y-0.5 p-0 text-xs text-gray-600 dark:text-gray-400">
                                                <li>• <span class="font-semibold">{{ __('messages.subscription_types.table.price') }}:</span> {{$item->price }} USD</li>
                                                <li>• <span class="font-semibold">{{ __('messages.subscription_types.table.users') }}:</span> {{ $item->amount }}</li>
                                                <li>• <span class="font-semibold">{{ __('messages.subscription_types.table.tag') }}:</span> {{ $item->tag }}</li>
                                            </ul>
                                        </td>
                                        <td class="text-center">
                                            <strong class="text-base text-gray-800 dark:text-gray-200">
                                            @if($item->duration == 1)
                                                {{ $item->duration }} <span class="text-xs font-normal text-gray-500">{{ __('messages.subscription_types.units.year') }}</span>
                                            @else
                                                {{ $item->duration }} <span class="text-xs font-normal text-gray-500">{{ __('messages.subscription_types.units.years') }}</span>
                                            @endif
                                            </strong>
                                        </td>
                                        <td>
                                            <div class="rounded border-l-4 border-purple-500 bg-gray-50 p-2 text-xs dark:bg-gray-700">
                                                <div class="space-y-0.5 text-gray-700 dark:text-gray-300">
                                                    <div>• <span class="font-semibold">{{ __('messages.subscription_types.table.duration') }}:</span>
                                                        @if($item->duration_discount == 1)
                                                            {{ round($item->duration_discount) }} {{ __('messages.subscription_types.units.year') }}
                                                        @else
                                                            {{ round($item->duration_discount) }} {{ __('messages.subscription_types.units.years') }}
                                                        @endif
                                                    </div>
                                                    <div>• <span class="font-semibold">{{ __('messages.subscription_types.table.discount') }}:</span> {{ $item->duration_value_discount }}%</div>
                                                    <div>• <span class="font-semibold">{{ __('messages.subscription_types.table.status') }}:</span>
                                                        @if ($item->state_dd == 1)
                                                            <span class="tw-badge tw-badge-green">{{ __('messages.subscription_types.status.active') }}</span>
                                                        @else
                                                            <span class="tw-badge tw-badge-red">{{ __('messages.subscription_types.status.inactive') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="rounded border-l-4 border-blue-500 bg-gray-50 p-2 text-xs dark:bg-gray-700">
                                                <div class="space-y-0.5 text-gray-700 dark:text-gray-300">
                                                    <div>• <span class="font-semibold">{{ __('messages.subscription_types.table.quantity') }}:</span>
                                                        @if ($item->number_stations_discount == 1)
                                                            {{ $item->number_stations_discount }} {{ __('messages.subscription_types.units.station') }}
                                                        @else
                                                            {{ $item->number_stations_discount }} {{ __('messages.subscription_types.units.stations') }}
                                                        @endif
                                                    </div>
                                                    <div>• <span class="font-semibold">{{ __('messages.subscription_types.table.discount') }}:</span> {{ $item->discount_value_stations }}%</div>
                                                    <div>• <span class="font-semibold">{{ __('messages.subscription_types.table.status') }}:</span>
                                                        @if ($item->state_de == 1)
                                                            <span class="tw-badge tw-badge-green">{{ __('messages.subscription_types.status.active') }}</span>
                                                        @else
                                                            <span class="tw-badge tw-badge-red">{{ __('messages.subscription_types.status.inactive') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if ($item->state == 1)
                                                <span class="tw-badge tw-badge-green">{{ __('messages.subscription_types.status.active') }}</span>
                                            @else
                                                <span class="tw-badge tw-badge-red">{{ __('messages.subscription_types.status.inactive') }}</span>
                                            @endif
                                        </td>
                                        @if(auth()->user()->hasAnyRole(['super_manager']))
                                        <td>
                                            <div class="flex justify-center gap-1">
                                                <button class="tw-btn-xs tw-btn-amber" wire:click='visibleUpdate({{$item->id}})' title="{{ __('messages.subscription_types.table.edit') }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button class="tw-btn-xs tw-btn-gray" wire:click="changeStatus({{ $item->id }},{{ $item->state }})" title="{{ $item->state == 1 ? __('messages.subscription_types.table.deactivate') : __('messages.subscription_types.table.activate') }}">
                                                    <i class="fa {{ $item->state == 1?'fa-toggle-off':'fa-toggle-on'}}"></i>
                                                </button>
                                                <button class="tw-btn-xs tw-btn-red" wire:click="deleteSubsEvent({{$item->id}})"
                                                    wire:confirm.prompt="{{ __('messages.subscription_types.table.delete_confirm', ['id' => $item->id]) }}|6" title="{{ __('messages.subscription_types.table.delete') }}">
                                                    <i class="fa fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                        @else
                                        <td>
                                            <div class="flex justify-center gap-1">
                                                <button class="tw-btn-xs tw-btn-amber" wire:click='visibleUpdate({{$item->id}})' title="{{ __('messages.subscription_types.table.edit') }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button class="tw-btn-xs tw-btn-gray" wire:click="changeStatus({{ $item->id }},{{ $item->state }})" title="{{ $item->state == 1 ? __('messages.subscription_types.table.deactivate') : __('messages.subscription_types.table.activate') }}">
                                                    <i class="fa {{ $item->state == 1?'fa-toggle-off':'fa-toggle-on'}}"></i>
                                                </button>
                                            </div>
                                        </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-gray-500 dark:text-gray-400">{{ __('messages.subscription_types.table.no_records') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-200 px-4 py-2 dark:border-gray-700">
                    {{ $data->links() }}
                </div>
            @endif

            @if($formAdd)
                <div class="flex justify-center py-4">
                    <div class="w-full max-w-2xl">
                        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="mb-3">
                                <label class="tw-label" for="name"><i class="fas fa-tag mr-1"></i>{{ __('messages.subscription_types.form.title') }}</label>
                                <input id="name" type="text" class="tw-input" placeholder="{{ __('messages.subscription_types.form.title_placeholder') }}" maxlength="40" required>
                            </div>
                            <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="tw-label" for="price"><i class="fas fa-dollar-sign mr-1"></i>{{ __('messages.subscription_types.form.price') }}</label>
                                    <input id="price" name="priceU" type="text" class="tw-input" placeholder="0.00" oninput="verifPrice()" maxlength="10" required>
                                </div>
                                <div>
                                    <label class="tw-label" for="duration"><i class="fas fa-calendar mr-1"></i>{{ __('messages.subscription_types.form.duration') }}</label>
                                    <input id="duration" type="text" class="tw-input" placeholder="1" oninput="verifDuration()" maxlength="2" required>
                                </div>
                            </div>
                            <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div>
                                    <label class="tw-label" for="amount"><i class="fas fa-users mr-1"></i>{{ __('messages.subscription_types.form.users') }}</label>
                                    <input id="amount" type="text" class="tw-input" placeholder="1" oninput="verifAmount()" maxlength="2" required>
                                </div>
                                <div>
                                    <label class="tw-label" for="state"><i class="fas fa-toggle-on mr-1"></i>{{ __('messages.subscription_types.form.status') }}</label>
                                    <select id="state" class="tw-input">
                                        <option value="1" selected>{{ __('messages.subscription_types.status.active') }}</option>
                                        <option value="0">{{ __('messages.subscription_types.status.inactive') }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="tw-label" for="tag"><i class="fas fa-bookmark mr-1"></i>{{ __('messages.subscription_types.form.tag') }}</label>
                                    <input id="tag" type="text" class="tw-input" placeholder="{{ __('messages.subscription_types.form.tag_placeholder') }}" oninput="verifTag()" maxlength="40" required>
                                </div>
                            </div>

                            <div class="my-4 rounded-lg bg-gradient-to-r from-blue-500 to-blue-700 px-4 py-2 text-center text-sm font-medium text-white shadow-sm">
                                <i class="fas fa-broadcast-tower mr-2"></i>{{ __('messages.subscription_types.form.section_stations') }}
                            </div>
                            <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="tw-label" for="discount_value_stations"><i class="fas fa-percent mr-1"></i>{{ __('messages.subscription_types.form.discount_percent') }}<x-info-tooltip title="{{ __('messages.subscription_types.tooltips.discount_stations.title') }}">{{ __('messages.subscription_types.tooltips.discount_stations.text') }}</x-info-tooltip></label>
                                    <input id="discount_value_stations" type="text" class="tw-input" placeholder="0" oninput="verifDiscountValueStation()" maxlength="3" value="{{$durationValueDiscountU}}">
                                </div>
                                <div>
                                    <label class="tw-label" for="number_stations_discount"><i class="fas fa-hashtag mr-1"></i>{{ __('messages.subscription_types.form.stations_quantity') }}<x-info-tooltip title="{{ __('messages.subscription_types.tooltips.stations_quantity.title') }}">{{ __('messages.subscription_types.tooltips.stations_quantity.text') }}</x-info-tooltip></label>
                                    <input id="number_stations_discount" type="text" class="tw-input" placeholder="" oninput="verifNumberStationsDiscount()" maxlength="4" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="tw-label" for="stateDE"><i class="fas fa-toggle-on mr-1"></i>{{ __('messages.subscription_types.form.discount_status') }}</label>
                                <select id="stateDE" class="tw-input">
                                    <option value="1" selected>{{ __('messages.subscription_types.status.active') }}</option>
                                    <option value="0">{{ __('messages.subscription_types.status.inactive') }}</option>
                                </select>
                            </div>

                            <div class="my-4 rounded-lg bg-gradient-to-r from-purple-500 to-purple-700 px-4 py-2 text-center text-sm font-medium text-white shadow-sm">
                                <i class="fas fa-calendar-alt mr-2"></i>{{ __('messages.subscription_types.form.section_duration') }}
                            </div>
                            <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="tw-label" for="duration_value_discount"><i class="fas fa-percent mr-1"></i>{{ __('messages.subscription_types.form.discount_in_percent') }}<x-info-tooltip title="{{ __('messages.subscription_types.tooltips.discount_duration.title') }}">{{ __('messages.subscription_types.tooltips.discount_duration.text') }}</x-info-tooltip></label>
                                    <input id="duration_value_discount" type="text" class="tw-input" placeholder="0" oninput="verifValueDurationDiscount()" maxlength="3">
                                </div>
                                <div>
                                    <label class="tw-label" for="duration_discount"><i class="fas fa-clock mr-1"></i>{{ __('messages.subscription_types.form.discount_duration_years') }}<x-info-tooltip title="{{ __('messages.subscription_types.tooltips.duration_years.title') }}">{{ __('messages.subscription_types.tooltips.duration_years.text') }}</x-info-tooltip></label>
                                    <input id="duration_discount" type="text" class="tw-input" placeholder="" oninput="verifdDurationDiscount()" maxlength="4" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="tw-label" for="stateDD"><i class="fas fa-toggle-on mr-1"></i>{{ __('messages.subscription_types.form.discount_status') }}</label>
                                <select id="stateDD" class="tw-input">
                                    <option value="1" selected>{{ __('messages.subscription_types.status.active') }}</option>
                                    <option value="0">{{ __('messages.subscription_types.status.inactive') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center gap-3 py-4">
                    <button class="tw-btn tw-btn-emerald" type="button" onclick="addSubscription()"><i class="fas fa-save mr-1"></i>{{ __('messages.subscription_types.buttons.register') }}</button>
                    <button class="tw-btn tw-btn-gray" wire:click='closeFormButton'><i class="fas fa-times mr-1"></i>{{ __('messages.subscription_types.buttons.close') }}</button>
                </div>
            @endif

            @if($formUpdate)
                <div class="flex justify-center py-4">
                    <div class="w-full max-w-2xl">
                        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="mb-3">
                                <label class="tw-label" for="nameU"><i class="fas fa-tag mr-1"></i>{{ __('messages.subscription_types.form.title') }}</label>
                                <input id="nameU" type="text" class="tw-input" placeholder="{{ __('messages.subscription_types.form.title_placeholder') }}" maxlength="40" value="{{$nameU}}" required>
                            </div>
                            <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="tw-label" for="priceU"><i class="fas fa-dollar-sign mr-1"></i>{{ __('messages.subscription_types.form.price') }}</label>
                                    <input id="priceU" name="priceU" type="text" class="tw-input" placeholder="" oninput="verifPriceU()" maxlength="10" value="{{$priceU}}" required>
                                </div>
                                <div>
                                    <label class="tw-label" for="durationU"><i class="fas fa-calendar mr-1"></i>{{ __('messages.subscription_types.form.duration') }}</label>
                                    <input id="durationU" type="text" class="tw-input" placeholder="" oninput="verifDurationU()" maxlength="2" value="{{$durationU}}" required>
                                </div>
                            </div>
                            <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div>
                                    <label class="tw-label" for="amountU"><i class="fas fa-users mr-1"></i>{{ __('messages.subscription_types.form.users') }}</label>
                                    <input id="amountU" type="text" class="tw-input" placeholder="" oninput="verifAmountU()" maxlength="2" value="{{$amountU}}" required>
                                </div>
                                <div>
                                    <label class="tw-label"><i class="fas fa-toggle-on mr-1"></i>{{ __('messages.subscription_types.form.status') }}</label>
                                    <select id="stateU" class="tw-input">
                                        @if($stateU=='1')
                                            <option value="1" selected>{{ __('messages.subscription_types.status.active') }}</option>
                                            <option value="0">{{ __('messages.subscription_types.status.inactive') }}</option>
                                        @else
                                            <option value="1">{{ __('messages.subscription_types.status.active') }}</option>
                                            <option value="0" selected>{{ __('messages.subscription_types.status.inactive') }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div>
                                    <label class="tw-label" for="tagU"><i class="fas fa-bookmark mr-1"></i>{{ __('messages.subscription_types.form.tag') }}</label>
                                    <input id="tagU" type="text" class="tw-input" placeholder="" oninput="verifTagU()" maxlength="40" value="{{$tagU}}" required>
                                </div>
                            </div>

                            <div class="my-4 rounded-lg bg-gradient-to-r from-blue-500 to-blue-700 px-4 py-2 text-center text-sm font-medium text-white shadow-sm">
                                <i class="fas fa-broadcast-tower mr-2"></i>{{ __('messages.subscription_types.form.section_stations') }}
                            </div>
                            <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="tw-label" for="discount_value_stationsU"><i class="fas fa-percent mr-1"></i>{{ __('messages.subscription_types.form.discount_percent') }}<x-info-tooltip title="{{ __('messages.subscription_types.tooltips.discount_stations.title') }}">{{ __('messages.subscription_types.tooltips.discount_stations.text') }}</x-info-tooltip></label>
                                    <input id="discount_value_stationsU" type="text" class="tw-input" placeholder="0" oninput="verifDiscountValueStationU()" maxlength="3" value="{{$durationValueDiscountU}}">
                                </div>
                                <div>
                                    <label class="tw-label" for="number_stations_discountU"><i class="fas fa-hashtag mr-1"></i>{{ __('messages.subscription_types.form.stations_quantity') }}<x-info-tooltip title="{{ __('messages.subscription_types.tooltips.stations_quantity.title') }}">{{ __('messages.subscription_types.tooltips.stations_quantity.text') }}</x-info-tooltip></label>
                                    <input id="number_stations_discountU" type="text" class="tw-input" placeholder="" oninput="verifNumberStationsDiscountU()" maxlength="4" value="{{$number_stations_discountU}}" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="tw-label" for="stateDEU"><i class="fas fa-toggle-on mr-1"></i>{{ __('messages.subscription_types.form.discount_status') }}</label>
                                <select id="stateDEU" class="tw-input">
                                    @if($stateDEU=='1')
                                        <option value="1" selected>{{ __('messages.subscription_types.status.active') }}</option>
                                        <option value="0">{{ __('messages.subscription_types.status.inactive') }}</option>
                                    @else
                                        <option value="1">{{ __('messages.subscription_types.status.active') }}</option>
                                        <option value="0" selected>{{ __('messages.subscription_types.status.inactive') }}</option>
                                    @endif
                                </select>
                            </div>

                            <div class="my-4 rounded-lg bg-gradient-to-r from-purple-500 to-purple-700 px-4 py-2 text-center text-sm font-medium text-white shadow-sm">
                                <i class="fas fa-calendar-alt mr-2"></i>{{ __('messages.subscription_types.form.section_duration') }}
                            </div>
                            <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="tw-label" for="duration_value_discountU"><i class="fas fa-percent mr-1"></i>{{ __('messages.subscription_types.form.discount_in_percent') }}<x-info-tooltip title="{{ __('messages.subscription_types.tooltips.discount_duration.title') }}">{{ __('messages.subscription_types.tooltips.discount_duration.text') }}</x-info-tooltip></label>
                                    <input id="duration_value_discountU" type="text" class="tw-input" placeholder="0" oninput="verifValueDurationDiscountU()" maxlength="3" value="{{$durationValueDiscountU}}">
                                </div>
                                <div>
                                    <label class="tw-label" for="duration_discountU"><i class="fas fa-clock mr-1"></i>{{ __('messages.subscription_types.form.discount_duration_years') }}<x-info-tooltip title="{{ __('messages.subscription_types.tooltips.duration_years.title') }}">{{ __('messages.subscription_types.tooltips.duration_years.text') }}</x-info-tooltip></label>
                                    <input id="duration_discountU" type="text" class="tw-input" placeholder="" oninput="verifdDurationDiscountU()" maxlength="4" value="{{$number_stations_discountU}}" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="tw-label" for="stateDDU"><i class="fas fa-toggle-on mr-1"></i>{{ __('messages.subscription_types.form.discount_status') }}</label>
                                <select id="stateDDU" class="tw-input">
                                    @if($stateDDU=='1')
                                        <option value="1" selected>{{ __('messages.subscription_types.status.active') }}</option>
                                        <option value="0">{{ __('messages.subscription_types.status.inactive') }}</option>
                                    @else
                                        <option value="1">{{ __('messages.subscription_types.status.active') }}</option>
                                        <option value="0" selected>{{ __('messages.subscription_types.status.inactive') }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center gap-3 py-4">
                    <button class="tw-btn tw-btn-blue" type="button" onclick="updateSubscription()"><i class="fas fa-sync-alt mr-1"></i>{{ __('messages.subscription_types.buttons.update') }}</button>
                    <button class="tw-btn tw-btn-gray" wire:click='closeFormButton'><i class="fas fa-times mr-1"></i>{{ __('messages.subscription_types.buttons.close') }}</button>
                </div>
            @endif
        </div>
    </div>
</div>
