<div>
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
                @if($viewType=='table')
                    @if($viewlist)
                        <div class="flex flex-wrap items-end gap-2">
                            <div>
                                <label class="tw-label">{{ __('messages.stations.filters.page') }}.</label>
                                <select wire:model.live="perPage" class="tw-input" style="width:auto">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <div>
                                <label class="tw-label">{{ __('messages.stations.filters.state') }}</label>
                                <select wire:model.live="stateChange" class="tw-input" style="width:auto">
                                    <option value="all">{{ __('messages.stations.filters.option_statechange_0') }}</option>
                                    <option value="1">{{ __('messages.stations.filters.option_statechange_1') }}</option>
                                    <option value="0">{{ __('messages.stations.filters.option_statechange_2') }}</option>
                                </select>
                            </div>
                            <div class="flex-1 min-w-40">
                                <label class="tw-label">{{ __('messages.stations.filters.search') }}:</label>
                                <input type="text" placeholder="{{ __('messages.stations.filters.search_station_name') }}" wire:model.live="search" class="tw-input">
                            </div>
                            <div class="flex-1 min-w-40">
                                <label class="tw-label">{{ __('messages.stations.filters.search_dir') }}:</label>
                                <input type="text" placeholder="{{ __('messages.stations.filters.search_placeholer_dir') }}" wire:model.live="searchLocation" class="tw-input">
                            </div>
                            <div class="ml-auto pt-4">
                                <button class="tw-btn tw-btn-emerald" wire:click='visibleAdd'><i class="fas fa-plus mr-1"></i>{{ __('messages.stations.filters.new_station') }}</button>
                            </div>
                        </div>
                    @endif
                   
                    @if($viewMaintenancePlan)
                        <div class="flex items-center justify-end gap-2">
                            @if(!$viewFormMaintenancePlanAdd && !$viewFormMaintenancePlanUpdate)
                                <button class="tw-btn-xs tw-btn-emerald" wire:click='setFormMaintanancePlan'>{{ __('messages.stations.maintananceplan.form_add_title') }}</button>
                            @endif
                            @if(!$viewFormMaintenancePlanUpdate)
                                <button class="tw-btn-xs tw-btn-gray" wire:click='goBack'>{{ __('messages.stations.update.go_back') }}</button>
                            @endif
                        </div>
                    @endif
                @endif
            </div>

            @if($viewlist)
            <div class="overflow-x-auto">
                @if($viewType=='table')
                    <div class="tw-table-wrap">
                        <table class="tw-table">
                            <thead class="themed-thead">
                                <tr>
                                    <th>{{ __('messages.stations.table.head_name') }}</th>
                                    <th>{{ __('messages.stations.table.head_managed_by') }}:</th>
                                    <th>{{ __('messages.stations.table.head_visibility') }}</th>
                                    <th class="text-center">{{ __('messages.stations.table.head_actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($data) > 0)
                                    @foreach ($data as $item)
                                        <tr>
                                            <td>
                                                <div>{{ $item->name }}</div>
                                                <small class="text-blue-500">{{ $item->location }}</small>
                                            </td>
                                            <td>
                                                <small class="font-semibold">{{ __('messages.stations.table.body_registered_by') }}:</small>
                                                <small>{{ $item->user_creator?->name ?? 'N/A' }}</small>
                                                @if($item->modifier_user!=null)
                                                    <br>
                                                    <small class="font-semibold">{{ __('messages.stations.table.body_changed_by') }}:</small>
                                                    <small>{{ $item->modifier_user?->name ?? 'N/A' }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->state == 1)
                                                    <span class="tw-badge tw-badge-green">{{ __('messages.stations.form.visibility_visible') }}</span>
                                                @else
                                                    <span class="tw-badge tw-badge-red">{{ __('messages.stations.form.visibility_hidden') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="flex items-center justify-center gap-1 flex-wrap">
                                                    {{-- Gestionar dropdown --}}
                                                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                                        <button @click="open = !open" class="tw-btn-xs tw-btn-amber">
                                                            {{ __('messages.stations.table.option_manage') }} <i class="fas fa-caret-down ml-1"></i>
                                                        </button>
                                                        <div x-show="open" x-cloak class="absolute left-0 z-50 mt-1 w-44 rounded border border-gray-200 bg-white shadow-lg dark:border-gray-600 dark:bg-gray-700">
                                                            <a class="block px-4 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-600" href="#" wire:click="setFormUpdateStation({{$item->id}})" @click="open=false"><i class="fa fa-edit mr-1"></i> {{ __('messages.stations.table.item_modify') }}</a>
                                                            <a class="block px-4 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-600" href="#" wire:click="setMaintenancePlan({{$item->id}})" @click="open=false"><i class="fa fa-edit mr-1"></i> {{ __('messages.stations.table.item_maintananceplan') }}</a>
                                                            <a class="block px-4 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-600" href="#" wire:click="openStationCalibrationModal({{$item->id}})" @click="open=false"><i class="fa fa-sliders-h mr-1"></i> {{ __('messages.stations.table.item_calibration') }}</a>
                                                        </div>
                                                    </div>
                                                    {{-- Reportes dropdown --}}
                                                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                                        <button @click="open = !open" class="tw-btn-xs tw-btn-gray">
                                                            {{ __('messages.stations.table.reports') }} <i class="fas fa-caret-down ml-1"></i>
                                                        </button>
                                                        <div x-show="open" x-cloak class="absolute left-0 z-50 mt-1 w-44 rounded border border-gray-200 bg-white shadow-lg dark:border-gray-600 dark:bg-gray-700">
                                                            <a class="block px-4 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-600" href="{{ route('reportStation', ['id' => $item->id, 'blade_type' => 'watch' ,'type' => 'station']) }}" target="_blank"><i class="fas fa-file-pdf mr-1"></i> {{ __('messages.stations.table.item_pdf') }}</a>
                                                        </div>
                                                    </div>
                                                    <button class="tw-btn-xs tw-btn-blue"
                                                            wire:click="openModalForecasts({{ $item->id }})"
                                                            title="{{ __('messages.stations.table.action_forecast') }}">
                                                        <i class="fas fa-cloud-sun"></i>
                                                    </button>
                                                    <button class="tw-btn-xs tw-btn-amber"
                                                            wire:click="openAlertModal({{ $item->id }})"
                                                            title="{{ __('messages.stations.table.action_alert') }}">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                    </button>
                                                    <a class="tw-btn-xs tw-btn-red" onclick="confirmDelete({{$item->id}})"><i class="fa fa-trash-alt"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="py-6 text-center text-gray-500">{{ __('messages.stations.table.no_data') }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="p-2">
                        @if(count($data) > 0)
                            {{ $data->links() }}
                        @endif
                    </div>

                @elseif($viewType=='steelseries')
                    <div class="px-2 pt-3">
                        <div class="mb-3 flex items-center gap-3 rounded border border-gray-200 bg-white p-2 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <button wire:loading.attr="disabled" class="tw-btn-xs tw-btn-gray" wire:click='openModalSteelSeries({{$dataForSteelseries['stationId']}})'>
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <div class="flex flex-1 items-center gap-3">
                                <span class="text-gray-800 dark:text-gray-200">{{$dataForSteelseries['name']}}</span>
                                <div wire:loading wire:target="openModalSteelSeries, returnToList" class="flex gap-1">
                                    <span class="inline-block h-2 w-2 animate-bounce rounded-full bg-gray-500" style="animation-delay:0s"></span>
                                    <span class="inline-block h-2 w-2 animate-bounce rounded-full bg-gray-500" style="animation-delay:.15s"></span>
                                    <span class="inline-block h-2 w-2 animate-bounce rounded-full bg-gray-500" style="animation-delay:.3s"></span>
                                </div>
                            </div>
                            <button class="tw-btn-xs tw-btn-gray" wire:click='returnToList'>{{ __('messages.stations.update.go_back') }}</button>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="w-full overflow-y-auto sm:w-1/4" style="max-height:80vh;">
                            <div class="flex flex-col items-center px-2 py-2">
                                <a href="javascript:void(0);" wire:click="handleClick('tempout')" class="flex flex-col items-center">
                                    <span>{{ __('messages.stations.map.more_data.temperature') }}</span>
                                    <steelseries-linear width="90" height="185" value="{{$dataForSteelseries['tempout']}}" gaugeType="TYPE2" titleString="{{ __('messages.stations.map.more_data.temperature') }}" unitString="°C" frameDesign="ANTHRACITE" backgroundColor="WHITE" lcdColor="WHITE" lcdDecimals="1"></steelseries-linear>
                                    <span class="text-gray-800">{{ __('messages.viewer.more_data.gauges.actual') }} {{$dataForSteelseries['tempout']}} °C</span>
                                    <span class="text-gray-800">{{ __('messages.viewer.more_data.gauges.max') }} {{$dataForSteelseries['tempoutmax']}} °C</span>
                                    <span class="text-gray-800">{{ __('messages.viewer.more_data.gauges.min') }} {{$dataForSteelseries['tempoutmin']}} °C</span>
                                </a>
                                <hr style="height:2px;background-color:rgb(102,102,102);border:none;width:100%;">
                                <a href="javascript:void(0);" wire:click="handleClick('humout')" class="flex flex-col items-center">
                                    <span>{{ __('messages.stations.map.more_data.humidity') }}</span>
                                    <steelseries-linear width="90" height="185" value="{{$dataForSteelseries['humout']}}" gaugeType="TYPE2" titleString="{{ __('messages.stations.map.more_data.humidity') }}" unitString="%" frameDesign="ANTHRACITE" backgroundColor="WHITE" lcdColor="WHITE" lcdDecimals="1"></steelseries-linear>
                                    <span class="text-gray-800">{{ __('messages.viewer.more_data.gauges.actual') }} {{$dataForSteelseries['humout']}} %</span>
                                </a>
                                <hr style="height:2px;background-color:rgb(102,102,102);border:none;width:100%;">
                                <a href="javascript:void(0);" wire:click="handleClick('raintotal')" class="flex flex-col items-center">
                                    <span>{{ __('messages.stations.map.more_data.precipitation') }}</span>
                                    <steelseries-linear width="90" height="185" value="{{$dataForSteelseries['raintotal']}}" gaugeType="TYPE2" titleString="{{ __('messages.stations.map.more_data.precipitation') }}" unitString="mm" frameDesign="ANTHRACITE" backgroundColor="WHITE" lcdColor="WHITE" lcdDecimals="1"></steelseries-linear>
                                    <span class="text-gray-800">{{ __('messages.viewer.more_data.gauges.actual') }} {{$dataForSteelseries['raintotal']}} mm</span>
                                </a>
                                <hr style="height:2px;background-color:rgb(102,102,102);border:none;width:100%;">
                                <a href="javascript:void(0);" wire:click="handleClick('dewptout')" class="flex flex-col items-center">
                                    <span>{{ __('messages.stations.map.more_data.dew_point') }}</span>
                                    <steelseries-linear width="90" height="185" value="{{$dataForSteelseries['dewptout']}}" gaugeType="TYPE2" titleString="{{ __('messages.stations.map.more_data.dew_point') }}" unitString="°C" frameDesign="ANTHRACITE" backgroundColor="WHITE" lcdColor="WHITE" lcdDecimals="1"></steelseries-linear>
                                    <span class="text-gray-800">{{ __('messages.viewer.more_data.gauges.actual') }} {{$dataForSteelseries['dewptout']}} °C</span>
                                </a>
                                <hr style="height:2px;background-color:rgb(102,102,102);border:none;width:100%;">
                                <a href="javascript:void(0);" wire:click="handleClick('winddir')" class="flex flex-col items-center">
                                    <span>{{ __('messages.stations.map.more_data.wind_direction') }}</span>
                                    <steelseries-compass size="150" value="{{$dataForSteelseries['winddir']}}" frameDesign="TILTED_BLACK" backgroundColor="WHITE" pointerType="TYPE2" pointerColor="RED" knobStyle="SILVER" titleString="{{ __('messages.stations.map.more_data.wind_direction') }}"></steelseries-compass>
                                    <span class="text-gray-800">{{ __('messages.viewer.more_data.gauges.actual') }} {{$dataForSteelseries['winddir']}} °</span>
                                </a>
                                <hr style="height:2px;background-color:rgb(102,102,102);border:none;width:100%;">
                                <a href="javascript:void(0);" wire:click="handleClick('windspeed')" class="flex flex-col items-center">
                                    <span>{{ __('messages.stations.map.more_data.wind_speed') }}</span>
                                    <steelseries-radial size="180" value="{{$dataForSteelseries['windspeed']}}" threshold="150" gaugeType="TYPE3" unitString="Km/h" frameDesign="ANTHRACITE" backgroundColor="LIGHT_GRAY" pointerType="TYPE6" knobStyle="SILVER" lcdColor="WHITE" lcdDecimals="1" foregroundType="TYPE2" tickLabelOrientation="NORMAL" titleString="{{ __('messages.stations.map.more_data.wind_speed') }}"></steelseries-radial>
                                    <span class="text-gray-800">{{ __('messages.viewer.more_data.gauges.actual') }} {{$dataForSteelseries['windspeed']}} Km/h</span>
                                </a>
                                <hr style="height:2px;background-color:rgb(102,102,102);border:none;width:100%;">
                                <a href="javascript:void(0);" wire:click="handleClick('uvindex')" class="flex flex-col items-center">
                                    <span>{{ __('messages.stations.map.more_data.uv_rays') }}</span>
                                    <steelseries-radial size="180" value="{{$dataForSteelseries['uvindex']}}" threshold="150" gaugeType="TYPE3" unitString="" frameDesign="ANTHRACITE" backgroundColor="LIGHT_GRAY" pointerType="TYPE6" knobStyle="SILVER" lcdColor="WHITE" lcdDecimals="1" foregroundType="TYPE2" tickLabelOrientation="NORMAL" titleString="{{ __('messages.stations.map.more_data.uv_rays') }}"></steelseries-radial>
                                    <span class="text-gray-800">{{ __('messages.viewer.more_data.gauges.actual') }} {{$dataForSteelseries['uvindex']}} </span>
                                </a>
                                <hr style="height:2px;background-color:rgb(102,102,102);border:none;width:100%;">
                                <a href="javascript:void(0);" wire:click="handleClick('press')" class="flex flex-col items-center">
                                    <span>{{ __('messages.stations.map.more_data.barometric_pressure') }}</span>
                                    <steelseries-radial size="180" value="{{$dataForSteelseries['press']}}" threshold="1000" gaugeType="TYPE3" unitString="mb" frameDesign="ANTHRACITE" backgroundColor="LIGHT_GRAY" pointerType="TYPE6" knobStyle="SILVER" lcdColor="WHITE" lcdDecimals="1" foregroundType="TYPE2" tickLabelOrientation="NORMAL" titleString="{{ __('messages.stations.map.more_data.barometric_pressure') }}"></steelseries-radial>
                                    <span class="text-gray-800">{{ __('messages.viewer.more_data.gauges.actual') }} {{$dataForSteelseries['press']}} mb</span>
                                </a>
                                <hr style="height:2px;background-color:rgb(102,102,102);border:none;width:100%;">
                                <a href="javascript:void(0);" wire:click="handleClick('solevo')" class="flex flex-col items-center">
                                    <span>{{ __('messages.stations.map.more_data.evapotranspiration') }}</span>
                                    <steelseries-radial size="180" value="{{$dataForSteelseries['solevo']}}" threshold="150" gaugeType="TYPE3" unitString="mm" frameDesign="ANTHRACITE" backgroundColor="LIGHT_GRAY" pointerType="TYPE6" knobStyle="SILVER" lcdColor="WHITE" lcdDecimals="1" foregroundType="TYPE2" tickLabelOrientation="NORMAL" titleString="{{ __('messages.stations.map.more_data.evapotranspiration') }}"></steelseries-radial>
                                    <span class="text-gray-800">{{ __('messages.viewer.more_data.gauges.actual') }} {{$dataForSteelseries['solevo']}} mm</span>
                                </a>
                                <hr style="height:2px;background-color:rgb(102,102,102);border:none;width:100%;">
                                <a href="javascript:void(0);" wire:click="handleClick('soiltemp1')" class="flex flex-col items-center">
                                    <span>{{ __('messages.stations.map.more_data.soil_temperature') }}</span>
                                    <steelseries-radial size="180" value="{{$dataForSteelseries['soiltemp1']}}" threshold="150" gaugeType="TYPE3" unitString="°C" frameDesign="ANTHRACITE" backgroundColor="LIGHT_GRAY" pointerType="TYPE6" knobStyle="SILVER" lcdColor="WHITE" lcdDecimals="1" foregroundType="TYPE2" tickLabelOrientation="NORMAL" titleString="{{ __('messages.stations.map.more_data.soil_temperature') }}"></steelseries-radial>
                                    <span class="text-gray-800">{{ __('messages.viewer.more_data.gauges.actual') }} {{$dataForSteelseries['soiltemp1']}} °C</span>
                                </a>
                                <hr style="height:2px;background-color:rgb(102,102,102);border:none;width:100%;">
                                <a href="javascript:void(0);" wire:click="handleClick('soilhum1')" class="flex flex-col items-center">
                                    <span>{{ __('messages.stations.map.more_data.soil_moisture') }}</span>
                                    <steelseries-radial size="180" value="{{$dataForSteelseries['soilhum1']}}" threshold="150" gaugeType="TYPE3" unitString="cb" frameDesign="ANTHRACITE" backgroundColor="LIGHT_GRAY" pointerType="TYPE6" knobStyle="SILVER" lcdColor="WHITE" lcdDecimals="1" foregroundType="TYPE2" tickLabelOrientation="NORMAL" titleString="{{ __('messages.stations.map.more_data.soil_moisture') }}"></steelseries-radial>
                                    <span class="text-gray-800">{{ __('messages.viewer.more_data.gauges.actual') }} {{$dataForSteelseries['soilhum1']}} cb</span>
                                </a>
                                <hr style="height:2px;background-color:rgb(102,102,102);border:none;width:100%;">
                                <a href="javascript:void(0);" wire:click="handleClick('leafhum1')" class="flex flex-col items-center">
                                    <span>{{ __('messages.stations.map.more_data.foliar_moisture') }}</span>
                                    <steelseries-radial size="180" value="{{$dataForSteelseries['leafhum1']}}" threshold="150" gaugeType="TYPE3" unitString="" frameDesign="ANTHRACITE" backgroundColor="LIGHT_GRAY" pointerType="TYPE6" knobStyle="SILVER" lcdColor="WHITE" lcdDecimals="1" foregroundType="TYPE2" tickLabelOrientation="NORMAL" titleString="{{ __('messages.stations.map.more_data.foliar_moisture') }}"></steelseries-radial>
                                    <span class="text-gray-800">{{ __('messages.viewer.more_data.gauges.actual') }} {{$dataForSteelseries['leafhum1']}} </span>
                                </a>
                            </div>
                        </div>
                        <div class="flex-1 p-2">
                            <div class="flex flex-wrap items-end gap-3 mb-3">
                                <div>
                                    <label class="tw-label">{{ __('messages.stations.map.more_data.date_range') }}:</label>
                                    <div class="flex gap-2">
                                        <input type="date" wire:model="selectedFromDate" class="tw-input" id="from-date-input">
                                        <input type="date" wire:model="selectedToDate" class="tw-input" id="to-date-input">
                                    </div>
                                </div>
                                <div>
                                    <label class="tw-label">{{ __('messages.stations.map.more_data.format') }}:</label>
                                    <select wire:model="formatType" class="tw-input" style="width:auto">
                                        <option value="all">{{ __('messages.stations.map.more_data.format_option_1') }}</option>
                                        <option value="daily">{{ __('messages.stations.map.more_data.format_option_2') }}</option>
                                    </select>
                                </div>
                                <div class="flex items-end gap-2">
                                    <div wire:loading class="mb-1"><span class="tw-spinner-sm"></span></div>
                                    <button class="tw-btn tw-btn-emerald" wire:click='viewGraph'>{{ __('messages.stations.map.more_data.view') }}</button>
                                    {{-- Actions dropdown --}}
                                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                        <button @click="open = !open" class="tw-btn tw-btn-gray">
                                            {{ __('messages.stations.map.more_data.actions') }} <i class="fas fa-caret-down ml-1"></i>
                                        </button>
                                        <div x-show="open" x-cloak class="absolute right-0 z-50 mt-1 w-44 rounded border border-gray-200 bg-white shadow-lg dark:border-gray-600 dark:bg-gray-700">
                                            <button class="block w-full px-4 py-1.5 text-left text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-600" type="button" onclick="resetZoom()">{{ __('messages.stations.map.more_data.restart_zoom') }}</button>
                                            <hr class="border-gray-200 dark:border-gray-600">
                                            <button class="block w-full px-4 py-1.5 text-left text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-600" type="button" onclick="downloadImage('png')">{{ __('messages.graphs.png_button') }}</button>
                                            <button class="block w-full px-4 py-1.5 text-left text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-600" type="button" onclick="downloadImage('jpeg')">{{ __('messages.graphs.jpeg_button') }}</button>
                                            <button class="block w-full px-4 py-1.5 text-left text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-600" type="button" onclick="downloadPDF()">{{ __('messages.graphs.pdf_button') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="position:relative;height:60vh;width:100%">
                                <canvas id="graph" height="60vh" width="250"></canvas>
                            </div>
                        </div>
                    </div>

                @elseif($viewType=='alerts')
                    <div class="px-2 pt-3">
                        <div class="mb-3 flex items-center gap-3 rounded border border-gray-200 bg-white p-2 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <button wire:loading.attr="disabled" class="tw-btn-xs tw-btn-gray" wire:click='getAlerts({{$varStationId}})'>
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <div class="flex flex-1 items-center gap-3">
                                <span class="text-gray-800 dark:text-gray-200">{{$nameStationAlert}}</span>
                                <div wire:loading wire:target="getAlerts, returnToList" class="flex gap-1">
                                    <span class="inline-block h-2 w-2 animate-bounce rounded-full bg-gray-500" style="animation-delay:0s"></span>
                                    <span class="inline-block h-2 w-2 animate-bounce rounded-full bg-gray-500" style="animation-delay:.15s"></span>
                                    <span class="inline-block h-2 w-2 animate-bounce rounded-full bg-gray-500" style="animation-delay:.3s"></span>
                                </div>
                            </div>
                            <button class="tw-btn-xs tw-btn-gray" wire:click='returnToList'>{{ __('messages.stations.update.go_back') }}</button>
                        </div>
                        <div class="mb-3 text-center text-lg font-bold text-gray-800 dark:text-gray-200">ALERTAS</div>
                        @if(count($dataAlerts) > 0)
                            <div class="tw-table-wrap">
                                <table class="tw-table">
                                    <thead class="themed-thead">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Alerta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dataAlerts as $item)
                                            <tr>
                                                <td class="font-semibold">{{$item->reg_date}}</td>
                                                <td>{{$item->description}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
            @endif

            @if($formAdd || $formUpdate)
                <div class="p-2">
                    <div class="mb-3 flex items-center rounded border border-gray-200 bg-white p-2 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <button wire:loading.attr="disabled" class="tw-btn-xs tw-btn-gray" @if($formAdd) wire:click='visibleAdd' @else wire:click='setFormUpdateStation({{ $idAU }})' @endif>
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <div class="flex flex-1 flex-col items-center justify-center px-2">
                            <h5 class="mb-0 text-center text-sm font-semibold uppercase text-gray-700 dark:text-gray-200">
                                {{ $formAdd ? __('messages.stations.form.title_add') : __('messages.stations.form.title_edit') }}
                            </h5>
                            <div wire:loading wire:target="goBack, visibleAdd, setFormUpdateStation" class="mt-1 flex gap-1">
                                <span class="inline-block h-2 w-2 animate-bounce rounded-full bg-gray-500" style="animation-delay:0s"></span>
                                <span class="inline-block h-2 w-2 animate-bounce rounded-full bg-gray-500" style="animation-delay:.15s"></span>
                                <span class="inline-block h-2 w-2 animate-bounce rounded-full bg-gray-500" style="animation-delay:.3s"></span>
                            </div>
                        </div>
                        <button class="tw-btn-xs tw-btn-gray" wire:click='goBack'>{{ __('messages.stations.update.go_back') }}</button>
                    </div>

                    @if($formAdd)
                    <form wire:submit.prevent="addStation" enctype="multipart/form-data">
                    @else
                    <form wire:submit.prevent="updateStation" enctype="multipart/form-data">
                    @endif

                        {{-- Station data section --}}
                        <div class="mb-4 rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="rounded-t-lg bg-blue-600 px-4 py-2 text-white">
                                <h5 class="mb-0 text-sm font-semibold">{{ __('messages.stations.form.section_titles.station_data') }}</h5>
                            </div>
                            <div class="p-4">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <div class="mb-3">
                                            <label class="tw-label" for="nameAU">{{ __('messages.stations.form.labels.station') }}:</label>
                                            <input type="text" id="nameAU" class="tw-input" wire:model="nameAU">
                                        </div>
                                        <div class="mb-3">
                                            <label class="tw-label" for="codeAU">{{ __('messages.stations.form.labels.station_code') }}:</label>
                                            <div class="flex gap-2">
                                                <input type="text" id="codeAU" class="tw-input" wire:model="codeAU" value="{{ $codeAU }}" readonly>
                                                <button class="tw-btn tw-btn-blue flex-shrink-0" type="button" wire:click="generateStationCode"
                                                        wire:target="generateStationCode"
                                                        wire:loading.attr="disabled"
                                                        wire:loading.class="opacity-50 cursor-not-allowed"
                                                        title="{{ __('messages.actions.generate') }} {{ __('messages.stations.form.labels.station_code') }}">
                                                    <i class="fas fa-sync-alt" wire:target="generateStationCode" wire:loading.class="fa-spin"></i> {{ __('messages.actions.generate') }}
                                                </button>
                                            </div>
                                            <small class="text-xs text-gray-500">{{ __('messages.stations.form.hints.station_code_unique') }}</small>
                                        </div>
                                        @if($formUpdate)
                                        <div class="mb-3">
                                            <label class="tw-label" for="locationAU">{{ __('messages.stations.form.labels.current_location') }}:</label>
                                            <input type="text" id="locationAU" class="tw-input" wire:model="locationAU" disabled>
                                        </div>
                                        @endif
                                        <div class="mb-3 grid grid-cols-3 gap-2">
                                            <div>
                                                <label class="tw-label" for="departmentIdAU">{{ __('messages.stations.form.labels.department') }}:</label>
                                                <select id="departmentIdAU" wire:model="departmentIdAU" wire:change="chooseDepartment" class="tw-input">
                                                    <option value="-1">{{ __('messages.common.select_option') }}</option>
                                                    @if(count($departmentAU) > 0)
                                                        @foreach($departmentAU as $dep)
                                                            <option value="{{$dep->id}}">{{$dep->name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div>
                                                <label class="tw-label" for="provinceIdAU">{{ __('messages.stations.form.labels.province') }}:</label>
                                                <select id="provinceIdAU" wire:model="provinceIdAU" wire:change="chooseProvince" class="tw-input">
                                                    <option value="-1">{{ __('messages.common.select_option') }}</option>
                                                    @if($departmentIdAU!=-1)
                                                        @if(count($provinceAU) > 0)
                                                            @foreach($provinceAU as $prov)
                                                                <option value="{{$prov->id}}">{{$prov->name}}</option>
                                                            @endforeach
                                                        @endif
                                                    @endif
                                                </select>
                                            </div>
                                            <div>
                                                <label class="tw-label" for="municipalityIdAU">{{ __('messages.stations.form.labels.municipality') }}:</label>
                                                <select id="municipalityIdAU" wire:model="municipalityIdAU" class="tw-input">
                                                    <option value="-1">{{ __('messages.common.select_option') }}</option>
                                                    @if($provinceIdAU!=-1)
                                                        @if(count($municipalityAU) > 0)
                                                            @foreach($municipalityAU as $mun)
                                                                <option value="{{$mun->id}}">{{$mun->name}}</option>
                                                            @endforeach
                                                        @endif
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3 grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="tw-label" for="placeAU">{{ __('messages.stations.form.labels.address') }}:</label>
                                                <input type="text" id="placeAU" class="tw-input" wire:model="placeAU">
                                            </div>
                                            <div>
                                                <label class="tw-label" for="elevationDT">{{ __('messages.stations.form.labels.elevation') }}:</label>
                                                <input type="text" id="elevationDT" class="tw-input" wire:model="elevationDT">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="tw-label" for="power_supplyDT">{{ __('messages.stations.form.labels.power_supply') }}:</label>
                                            <select id="power_supplyDT" wire:model="power_supplyDT" class="tw-input">
                                                <option value="-1">{{ __('messages.common.select_option') }}</option>
                                                <option value="Photovoltaic">{{ $optionLabelMap['Photovoltaic'] ?? 'Photovoltaic' }}</option>
                                                <option value="Electric">{{ $optionLabelMap['Electric'] ?? 'Electric' }}</option>
                                            </select>
                                        </div>
                                        <div class="mb-3 grid grid-cols-3 gap-2">
                                            <div>
                                                <label class="tw-label" for="brandIdDT">{{ __('messages.stations.form.labels.brand') }}:</label>
                                                <select id="brandIdDT" wire:model="brandIdDT" wire:change="chooseBrand" class="tw-input">
                                                    <option value="-1">{{ __('messages.common.select_option') }}</option>
                                                    @foreach($brandsListDT as $brand)
                                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="tw-label" for="stationModelIdDT">{{ __('messages.stations.form.labels.model') }}:</label>
                                                <select id="stationModelIdDT" wire:model="stationModelIdDT" class="tw-input"
                                                        @if($brandIdDT == -1 || count($modelsListDT) === 0) disabled @endif>
                                                    <option value="-1">{{ __('messages.common.select_option') }}</option>
                                                    @foreach($modelsListDT as $stModel)
                                                        <option value="{{ $stModel->id }}">{{ $stModel->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="tw-label" for="installation_dateDT">{{ __('messages.stations.form.labels.installation_date_short') }}:</label>
                                                <input type="date" id="installation_dateDT" class="tw-input" wire:model="installation_dateDT">
                                            </div>
                                        </div>
                                        <div class="mb-3 grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="tw-label">{{ __('messages.stations.form.visibility_label') }}:</label>
                                                <select wire:model="stateAU" class="tw-input">
                                                    <option value="1">{{ __('messages.stations.form.visibility_visible') }}</option>
                                                    <option value="0">{{ __('messages.stations.form.visibility_hidden') }}</option>
                                                </select>
                                            </div>
                                            @if($formUpdate)
                                            <div>
                                                <label class="tw-label">{{ __('messages.stations.form.translation_label') }}:</label>
                                                <select wire:model="isTranslation" class="tw-input">
                                                    <option value="-1">{{ __('messages.stations.form.translation_none') }}</option>
                                                    <option value="1">{{ __('messages.stations.form.translation_apply') }}</option>
                                                </select>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <div class="mb-3 grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="tw-label" for="latitudeAU">{{ __('messages.stations.form.latitude_label') }}: </label>
                                                <input class="tw-input bg-amber-50" type="text" id="latitudeAU" wire:model='latitudeAU' placeholder="{{ __('messages.stations.form.latitude_placeholder') }}">
                                            </div>
                                            <div>
                                                <label class="tw-label" for="longitudeAU">{{ __('messages.stations.form.longitude_label') }}: </label>
                                                <input class="tw-input bg-amber-50" type="text" id="longitudeAU" wire:model='longitudeAU' placeholder="{{ __('messages.stations.form.longitude_placeholder') }}">
                                            </div>
                                        </div>
                                        <div class="mb-3" wire:ignore>
                                            <label class="tw-label">{{ __('messages.stations.form.map_label') }}:</label>
                                            <div id="stationFormMap" class="w-full rounded border border-gray-200 dark:border-gray-700" style="height:300px;"></div>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('messages.stations.form.map_hint') }}</p>
                                        </div>
                                        @if(count($listTranslation) > 0)
                                            <div style="max-height:50vh;overflow-y:auto;" class="rounded border border-gray-200 dark:border-gray-700">
                                                <table class="tw-table">
                                                    <caption class="caption-top py-1 text-center text-sm font-semibold text-gray-700">{{ __('messages.stations.form.history_title') }}</caption>
                                                    <thead class="themed-thead">
                                                        <tr>
                                                            <th>{{ __('messages.stations.form.history_header_registered') }}</th>
                                                            <th>{{ __('messages.stations.form.history_header_description') }}</th>
                                                            <th>{{ __('messages.stations.form.history_header_responsible') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($listTranslation as $item)
                                                            <tr>
                                                                <td>{{$item->reg_date}}</td>
                                                                <td>{{$item->description}}</td>
                                                                <td>{{$item->user->name}} {{$item->user->lastname}}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Gallery section --}}
                        <div class="mb-4 rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="border-b border-gray-200 bg-gray-50 px-4 py-2 dark:border-gray-700 dark:bg-gray-700">
                                <h5 class="mb-0 text-sm font-semibold text-blue-600 dark:text-blue-400">{{ __('messages.stations.form.gallery_title') }}</h5>
                            </div>
                            <div class="p-4">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                    <div class="rounded border border-gray-200 p-3 text-center dark:border-gray-700">
                                        <div class="mb-2 flex h-32 items-center justify-center">
                                            <div wire:loading wire:target="image1DT" class="text-gray-500"><span class="tw-spinner-sm"></span></div>
                                            <div wire:loading.remove wire:target="image1DT" class="w-full">
                                                @if($image1DT)
                                                    <img src="{{ $image1DT->temporaryUrl() }}" class="mx-auto max-h-32 rounded" alt="imagen_estacion1">
                                                @elseif($image1linkDT === \App\Livewire\Station\Station::DEFAULT_IMAGE_PATH)
                                                    <img src="{{ asset($image1linkDT) }}" class="mx-auto max-h-32 rounded" alt="imagen_estacion1">
                                                @else
                                                    <img src="{{ asset("/storage/".$image1linkDT) }}" class="mx-auto max-h-32 rounded" alt="imagen_estacion1">
                                                @endif
                                            </div>
                                        </div>
                                        <label class="tw-label text-left" for="image1Input">{{ __('messages.stations.form.gallery_front_label') }}</label>
                                        <input type="file" wire:model="image1DT" id="image1Input" accept="image/png, image/jpeg, image/webp, image/*" class="w-full text-sm">
                                    </div>
                                    <div class="rounded border border-gray-200 p-3 text-center dark:border-gray-700">
                                        <div class="mb-2 flex h-32 items-center justify-center">
                                            <div wire:loading wire:target="image2DT" class="text-gray-500"><span class="tw-spinner-sm"></span></div>
                                            <div wire:loading.remove wire:target="image2DT" class="w-full">
                                                @if($image2DT)
                                                    <img src="{{ $image2DT->temporaryUrl() }}" class="mx-auto max-h-32 rounded" alt="imagen_estacion2">
                                                @elseif($image2linkDT === \App\Livewire\Station\Station::DEFAULT_IMAGE_PATH)
                                                    <img src="{{ asset($image2linkDT) }}" class="mx-auto max-h-32 rounded" alt="imagen_estacion2">
                                                @else
                                                    <img src="{{ asset("/storage/".$image2linkDT) }}" class="mx-auto max-h-32 rounded" alt="imagen_estacion2">
                                                @endif
                                            </div>
                                        </div>
                                        <label class="tw-label text-left" for="image2Input">{{ __('messages.stations.form.gallery_side_label') }}</label>
                                        <input type="file" wire:model="image2DT" id="image2Input" accept="image/png, image/jpeg, image/webp, image/*" class="w-full text-sm">
                                    </div>
                                    <div class="rounded border border-gray-200 p-3 text-center dark:border-gray-700">
                                        <div class="mb-2 flex h-32 items-center justify-center">
                                            <div wire:loading wire:target="image3DT" class="text-gray-500"><span class="tw-spinner-sm"></span></div>
                                            <div wire:loading.remove wire:target="image3DT" class="w-full">
                                                @if($image3DT)
                                                    <img src="{{ $image3DT->temporaryUrl() }}" class="mx-auto max-h-32 rounded" alt="imagen_estacion3">
                                                @elseif($image3linkDT === \App\Livewire\Station\Station::DEFAULT_IMAGE_PATH)
                                                    <img src="{{ asset($image3linkDT) }}" class="mx-auto max-h-32 rounded" alt="imagen_estacion3">
                                                @else
                                                    <img src="{{ asset("/storage/".$image3linkDT) }}" class="mx-auto max-h-32 rounded" alt="imagen_estacion3">
                                                @endif
                                            </div>
                                        </div>
                                        <label class="tw-label text-left" for="image3Input">{{ __('messages.stations.form.gallery_detail_label') }}</label>
                                        <input type="file" wire:model="image3DT" id="image3Input" accept="image/png, image/jpeg, image/webp, image/*" class="w-full text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-center gap-3 pb-4">
                            <div wire:loading><span class="tw-spinner-sm"></span></div>
                            <button class="tw-btn px-6 {{ $formAdd ? 'tw-btn-emerald' : 'tw-btn-blue' }}" type="submit"
                                    wire:target="image1DT,image2DT,image3DT,addStation,updateStation"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-not-allowed">
                                {{ mb_strtoupper($formAdd ? __('messages.actions.save') : __('messages.actions.update')) }}
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            @if($viewMaintenancePlan)
                @if($viewFormMaintenancePlanAdd || $viewFormMaintenancePlanUpdate)
                    <div class="rounded-lg bg-gray-100 p-4 dark:bg-gray-700">
                        @if($viewFormMaintenancePlanAdd)
                            <div class="mb-3 flex items-center justify-end gap-2">
                                <button class="tw-btn tw-btn-emerald" onclick='registerFormMaintanancePlan()'>{{ __('messages.actions.save') }}</button>
                                <button class="tw-btn tw-btn-gray" wire:click='closeFormMaintanancePlan'>{{ __('messages.stations.maintananceplan.text.close_form') }}</button>
                            </div>
                        @endif
                        <div class="mb-3 text-center">
                            @if($viewFormMaintenancePlanAdd)
                                <div class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ __('messages.stations.maintananceplan.text.fill_form') }}</div>
                            @else
                                <div class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ __('messages.stations.maintananceplan.text.update_form') }}</div>
                            @endif
                            <div class="text-base font-semibold text-blue-600">{{$stationNameMP}}</div>
                        </div>

                        <div class="mb-3 grid grid-cols-1 gap-3 md:grid-cols-3">
                            <div>
                                <label class="tw-label" for="maintenanceDate">Fecha registro*:</label>
                                <input type="date" id="maintenanceDate" class="tw-input" wire:model.live="maintenanceDate">
                            </div>
                            <div>
                                <label class="tw-label" for="nextMaintenanceDate">{{ __('messages.stations.form.labels.next_maintenance_required') }}:</label>
                                <input type="date" id="nextMaintenanceDate" class="tw-input" wire:model.live="nextMaintenanceDate">
                            </div>
                            <div class="grid grid-cols-1 gap-2">
                                <div>
                                    <label class="tw-label" for="workOrder">{{ __('messages.stations.form.labels.work_order_required') }}:</label>
                                    <input type="text" id="workOrder" class="tw-input" wire:model="workOrder">
                                </div>
                                <div>
                                    <label class="tw-label" for="deliveryNote">{{ __('messages.stations.form.labels.delivery_note_required') }}:</label>
                                    <input type="text" id="deliveryNote" class="tw-input" wire:model="deliveryNote">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 grid grid-cols-1 gap-3 md:grid-cols-2">
                            <div>
                                <label class="tw-label" for="preventiveMaintenance">{{ __('messages.stations.form.labels.preventive_maintenance_required') }}:</label>
                                <select id="preventiveMaintenance" wire:model.live="preventiveMaintenance" class="tw-input">
                                    <option value="-1">{{ __('messages.common.select_option') }}</option>
                                    <option value="SI">{{ $optionLabelMap['SI'] ?? 'SI' }}</option>
                                    <option value="NO">{{ $optionLabelMap['NO'] ?? 'NO' }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="tw-label" for="correctiveMaintenance">{{ __('messages.stations.form.labels.corrective_maintenance_required') }}:</label>
                                <select id="correctiveMaintenance" wire:model="correctiveMaintenance" class="tw-input">
                                    <option value="-1">{{ __('messages.common.select_option') }}</option>
                                    <option value="SI">{{ $optionLabelMap['SI'] ?? 'SI' }}</option>
                                    <option value="NO">{{ $optionLabelMap['NO'] ?? 'NO' }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="tw-label" for="descriptionMaintenance">{{ __('messages.stations.form.labels.description_required') }}:</label>
                            <textarea style="height:100px;width:100%" class="tw-input" wire:model="descriptionMaintenance"></textarea>
                        </div>
                        <div class="mb-3 grid grid-cols-1 gap-3 md:grid-cols-3">
                            <div>
                                <label class="tw-label" for="recommendations">{{ __('messages.stations.form.labels.recommendations') }}:</label>
                                <textarea style="height:150px;width:100%" class="tw-input" wire:model="recommendations"></textarea>
                            </div>
                            <div class="flex flex-col items-center">
                                <label class="tw-label" for="image_before">{{ __('messages.stations.form.labels.before_maintenance_required') }}</label>
                                @if($image_before)
                                    <img style="display:block;width:50%;height:auto;" src="{{ $image_before->temporaryUrl() }}" alt="image_before">
                                @else
                                    @if($viewFormMaintenancePlanAdd)
                                        <img src="{{ asset(\App\Livewire\Station\Station::DEFAULT_IMAGE_PATH) }}" class="max-w-24 rounded border" alt="image_before">
                                    @else
                                        <img src="{{ $imagebeforelink==null?asset(\App\Livewire\Station\Station::DEFAULT_IMAGE_PATH):asset('storage/'.$imagebeforelink) }}" class="max-w-24 rounded border" alt="image_before">
                                    @endif
                                @endif
                                <input type="file" wire:model.live="image_before" id="image_before" accept="image/png, image/jpeg, image/webp, image/*" class="mt-2 w-full text-sm">
                            </div>
                            <div class="flex flex-col items-center">
                                <label class="tw-label" for="image_after">{{ __('messages.stations.form.labels.after_maintenance_required') }}</label>
                                @if($image_after)
                                    <img style="display:block;width:50%;height:auto;" src="{{ $image_after->temporaryUrl() }}" alt="image_after">
                                @else
                                    @if($viewFormMaintenancePlanAdd)
                                        <img src="{{ asset(\App\Livewire\Station\Station::DEFAULT_IMAGE_PATH) }}" class="max-w-24 rounded border" alt="image_after">
                                    @else
                                        <img src="{{ $imageafterlink==null?asset(\App\Livewire\Station\Station::DEFAULT_IMAGE_PATH):asset('storage/'.$imageafterlink) }}" class="max-w-24 rounded border" alt="image_after">
                                    @endif
                                @endif
                                <input type="file" wire:model.live="image_after" id="image_after" accept="image/png, image/jpeg, image/webp, image/*" class="mt-2 w-full text-sm">
                            </div>
                        </div>

                        <div class="mb-3 text-center text-lg font-bold text-gray-800 dark:text-gray-200">Firma*:</div>
                        @if($viewFormMaintenancePlanAdd)
                            <div class="mb-3 flex justify-center">
                                <canvas wire:init="loadCanvas" style="background-color:rgb(203,199,199);margin:0 auto;" id="signature-pad" width="500" height="200"></canvas>
                            </div>
                            <div class="mb-4 flex justify-center">
                                <button class="tw-btn tw-btn-amber" type="button" onclick="signatureErase()">Borrar</button>
                            </div>
                        @else
                            <div class="mb-3 flex justify-center">
                                <div class="w-full md:w-1/3">
                                    <img src="{{asset('storage/'.$image_signatureU)}}" alt="signature">
                                </div>
                            </div>
                            <div class="mb-3 flex justify-end gap-2">
                                <button class="tw-btn tw-btn-blue" type="button" wire:click='updateFormMaintanancePlan()'>{{ __('messages.actions.update') }}</button>
                                <button class="tw-btn tw-btn-gray" type="button" wire:click='closeFormMaintanancePlan'>{{ __('messages.stations.maintananceplan.text.close_form') }}</button>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="p-4">
                        <div class="mb-3 text-center">
                            <span class="inline-block rounded-full bg-gray-100 px-5 py-1.5 text-xs font-semibold uppercase tracking-wide text-teal-700 dark:bg-gray-700 dark:text-teal-300">PLAN DE MANTENIMIENTO</span>
                        </div>
                        <div class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-3">
                            <div>
                                <label class="tw-label" for="stationNameMP">{{ __('messages.stations.form.labels.station') }}:</label>
                                <input type="text" id="stationNameMP" class="tw-input" wire:model="stationNameMP" disabled>
                            </div>
                            <div>
                                <label class="tw-label" for="locationMP">{{ __('messages.viewer.station_info.fields.location') }}:</label>
                                <input type="text" id="locationMP" class="tw-input" wire:model="locationMP" disabled>
                            </div>
                            <div>
                                <label class="tw-label" for="reg_dateMP">{{ __('messages.stations.form.labels.installation_date') }}:</label>
                                <input type="date" id="reg_dateMP" class="tw-input" wire:model="reg_dateMP" disabled>
                            </div>
                        </div>

                        <div class="mb-3 text-center">
                            <span class="inline-block rounded-full bg-gray-100 px-5 py-1.5 text-xs font-semibold uppercase tracking-wide text-teal-700 dark:bg-gray-700 dark:text-teal-300">HISTORIAL DE TRABAJOS</span>
                        </div>
                        <div class="mb-3 flex flex-wrap items-end gap-3 rounded bg-gray-200 p-3 dark:bg-gray-700">
                            <div>
                                <label class="tw-label">cantidad Item.</label>
                                <select wire:model.live="paginationMP" class="tw-input" style="width:auto">
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <div>
                                <label class="tw-label">Buscar por fecha:</label>
                                <input type="date" placeholder="{{ __('messages.stations.maintananceplan.filters.maintenance_date_placeholder') }}" wire:model.live="maintenanceDateFilter" class="tw-input">
                            </div>
                            <div>
                                <label class="tw-label"># Orden de trabajo:</label>
                                <input type="text" placeholder="{{ __('messages.stations.maintananceplan.filters.work_order_placeholder') }}" wire:model.live="workOrderFilter" class="tw-input">
                            </div>
                            <div>
                                <label class="tw-label"># Nota de venta:</label>
                                <input type="text" placeholder="{{ __('messages.stations.maintananceplan.filters.delivery_note_placeholder') }}" wire:model.live="deliveryNoteFilter" class="tw-input">
                            </div>
                            <div>
                                <label class="tw-label">Tipo de mantenimiento</label>
                                <select wire:model.live="maintenanceTypeNoteFilter" class="tw-input" style="width:auto">
                                    <option value="-1">{{ __('messages.common.select_option') }}</option>
                                    <option value="preventive">{{ __('messages.stations.maintananceplan.table.preventive') }}</option>
                                    <option value="corrective">{{ __('messages.stations.maintananceplan.table.corrective') }}</option>
                                </select>
                            </div>
                            @if(count($dataResponse)>0)
                                <div class="ml-auto">
                                    <a class="tw-btn tw-btn-red" href="{{ route('reportMaintenanceFilter', ['station_id' => $stationIdMP,'pagination' => $paginationMP,'maintenance_date' => $maintenanceDateFilter,'work_order' => $workOrderFilter,'delivery_note' => $deliveryNoteFilter,'maintenance_type' => $maintenanceTypeNoteFilter, 'type' => 'watch']) }}" target="_blank">{{ __('messages.actions.generate_pdf') }}</a>
                                </div>
                            @endif
                        </div>

                        <div class="tw-table-wrap">
                            <table class="tw-table">
                                <thead class="themed-thead">
                                    <tr>
                                        <th>{{ __('messages.stations.maintananceplan.table.codes') }}</th>
                                        <th>{{ __('messages.stations.maintananceplan.table.maintenance_date') }}</th>
                                        <th>{{ __('messages.stations.maintananceplan.table.description') }}</th>
                                        <th>{{ __('messages.stations.maintananceplan.table.recommendations') }}</th>
                                        <th>{{ __('messages.stations.maintananceplan.table.maintenance_type') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($dataResponse)>0)
                                        @foreach($dataResponse as $item)
                                        <tr>
                                            <td>
                                                <div>▪ {{ __('messages.stations.form.labels.work_order_number') }}: <span class="text-blue-600">{{$item->work_order_number}}</span></div>
                                                <div>▪ {{ __('messages.stations.form.labels.sale_note_number') }}: <span class="text-blue-600">{{$item->delivery_note_number}}</span></div>
                                            </td>
                                            <td>
                                                <div>▪ {{ __('messages.stations.form.labels.registration_date') }}: <span class="text-blue-600">{{$item->maintenance_date}}</span></div>
                                                <div>▪ {{ __('messages.stations.form.labels.next_maintenance') }}: <span class="text-blue-600">{{$item->next_maintenance_date}}</span></div>
                                            </td>
                                            <td><textarea cols="20" rows="5" disabled>{{$item->description}}</textarea></td>
                                            <td><textarea cols="20" rows="5" disabled>{{$item->recommendations}}</textarea></td>
                                            <td>
                                                <div>▪ {{ __('messages.stations.maintananceplan.table.preventive') }}: <span class="text-blue-600">{{$item->preventive_maintenance}}</span></div>
                                                <div>▪ {{ __('messages.stations.maintananceplan.table.corrective') }}: <span class="text-blue-600">{{$item->corrective_maintenance}}</span></div>
                                            </td>
                                            <td class="bg-gray-300 dark:bg-gray-600">
                                                {{-- Actions dropdown --}}
                                                <div class="relative mb-2" x-data="{ open: false }" @click.outside="open = false">
                                                    <button @click="open = !open" class="tw-btn-xs tw-btn-gray">
                                                        {{ __('messages.stations.maintananceplan.table.actions') }} <i class="fas fa-caret-down ml-1"></i>
                                                    </button>
                                                    <div x-show="open" x-cloak class="absolute right-0 z-50 mt-1 w-44 rounded border border-gray-200 bg-white shadow-lg dark:border-gray-600 dark:bg-gray-700">
                                                        <a class="block px-4 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-600" href="{{ route('reportMaintenanceIndividual', ['id' => $item->id, 'type' => 'watch']) }}" target="_blank">{{ __('messages.stations.table.item_pdf') }}</a>
                                                        <div class="block cursor-pointer px-4 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-600" wire:click="setFormMaintanancePlanU({{$item->id}})" @click="open=false">{{ __('messages.actions.edit') }}</div>
                                                        <div class="block cursor-pointer px-4 py-1.5 text-sm text-red-600 hover:bg-gray-50 dark:text-red-400 dark:hover:bg-gray-600" wire:click="deleteMaintenancePlan({{$item->id}})" wire:confirm="{{ __('messages.actions.confirm_delete') }}" @click="open=false">{{ __('messages.actions.delete') }}</div>
                                                    </div>
                                                </div>
                                                <div class="flex justify-center gap-1">
                                                    @if($item->image_before==null)
                                                        <div class="cursor-pointer" onclick="viewAndDownloadImage('{{ __('messages.stations.maintananceplan.images.before') }}','{{ asset($imagebeforelink) }}')"><img src="{{ asset($imagebeforelink) }}" class="rounded border" alt="imagen_before" width="50" height="50"></div>
                                                    @else
                                                        <div class="cursor-pointer" onclick="viewAndDownloadImage('{{ __('messages.stations.maintananceplan.images.before') }}','{{ asset('storage/'.$item->image_before) }}')"><img src="{{ asset('storage/'.$item->image_before) }}" class="rounded border" alt="imagen_before" width="50" height="50"></div>
                                                    @endif
                                                    @if($item->image_after==null)
                                                        <div class="cursor-pointer" onclick="viewAndDownloadImage('{{ __('messages.stations.maintananceplan.images.after') }}','{{ asset($imageafterlink) }}')"><img src="{{ asset($imageafterlink) }}" class="rounded border" alt="image_after" width="50" height="50"></div>
                                                    @else
                                                        <div class="cursor-pointer" onclick="viewAndDownloadImage('{{ __('messages.stations.maintananceplan.images.after') }}','{{ asset('storage/'.$item->image_after) }}')"><img src="{{ asset('storage/'.$item->image_after) }}" class="rounded border" alt="image_after" width="50" height="50"></div>
                                                    @endif
                                                </div>
                                                <div class="mt-1 flex justify-center">
                                                    @if($item->image_signature==null)
                                                        <div class="cursor-pointer" onclick="viewAndDownloadImage('{{ __('messages.stations.maintananceplan.images.signature') }}','{{ asset($imageafterlink) }}')"><img src="{{ asset($imageafterlink) }}" class="rounded border" alt="image_signature" width="50" height="50"></div>
                                                    @else
                                                        <div class="cursor-pointer" onclick="viewAndDownloadImage('{{ __('messages.stations.maintananceplan.images.signature') }}','{{ asset('storage/'.$item->image_signature) }}')"><img src="{{ asset('storage/'.$item->image_signature) }}" class="rounded border" alt="image_signature" width="50"></div>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="py-6 text-center text-gray-500" colspan="8">{{ __('messages.stations.table.no_data') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        {{-- Modal de calibración (Tailwind/Alpine) --}}
        @if($showCalibrationModal)
        <x-tw-modal close="closeStationCalibrationModal" max-width="6xl"
                    header-class="bg-emerald-600 text-white"
                    :title="__('messages.stations.calibration.title') . ($stationCalibrationStationName ? ' - '.$stationCalibrationStationName : '')">
                    <div>
                        <div class="mb-3 grid grid-cols-1 gap-3 md:grid-cols-3">
                            <div>
                                <label class="tw-label">{{ __('messages.stations.form.labels.station') }}:</label>
                                <input type="text" class="tw-input" value="{{ $stationCalibrationStationName }}" disabled>
                            </div>
                            <div>
                                <label class="tw-label">{{ __('messages.viewer.station_info.fields.location') }}:</label>
                                <input type="text" class="tw-input" value="{{ $stationCalibrationLocation }}" disabled>
                            </div>
                            <div>
                                <label class="tw-label">{{ __('messages.stations.form.labels.installation_date') }}:</label>
                                <input type="text" class="tw-input" value="{{ $stationCalibrationRegDate }}" disabled>
                            </div>
                        </div>

                        <div class="tw-alert tw-alert-blue text-xs">
                            {{ __('messages.stations.calibration.note') }}
                            <code>App\Services\StationCalibrationService</code>.
                        </div>

                        <div class="mb-3 flex items-center justify-between">
                            <h6 class="mb-0 font-semibold text-gray-800">{{ __('messages.stations.calibration.table_title') }}</h6>
                            @if(!$viewStationCalibrationForm)
                                <button class="tw-btn tw-btn-emerald" wire:click="setStationCalibrationFormAdd" @if(count($stationCalibrationAvailableVariables) === 0) disabled @endif>
                                    {{ __('messages.stations.calibration.add_button') }}
                                </button>
                            @endif
                        </div>

                        <div class="tw-table-wrap mb-3">
                            <table class="tw-table">
                                <thead class="themed-thead">
                                    <tr>
                                        <th>{{ __('messages.stations.calibration.table.variable') }}</th>
                                        <th>{{ __('messages.stations.calibration.table.key') }}</th>
                                        <th>
                                            {{ __('messages.stations.calibration.table.params') }}
                                            <span class="relative inline-block" x-data="{ open: false }" @click.outside="open = false">
                                                <button type="button" class="tw-btn-link text-sm p-0 ml-1 align-baseline" @click="open = !open">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                                <div x-show="open" x-cloak class="absolute left-0 z-50 mt-1 w-72 rounded border border-gray-200 bg-white p-3 text-left text-xs font-normal text-gray-700 shadow-lg dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                                                    <strong class="mb-1 block">{{ __('messages.stations.calibration.param_help.title') }}</strong>
                                                    <div>{!! __('messages.stations.calibration.param_help.content') !!}</div>
                                                </div>
                                            </span>
                                        </th>
                                        <th>{{ __('messages.stations.calibration.table.state') }}</th>
                                        <th class="text-center">{{ __('messages.stations.calibration.table.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($stationCalibrationRows) > 0)
                                        @foreach($stationCalibrationRows as $item)
                                            <tr>
                                                <td>{{ $stationCalibrationVariableOptions[$item->variable_name] ?? $item->variable_name }}</td>
                                                <td><code>{{ $item->calibration_key }}</code></td>
                                                <td>
                                                    @if(!empty($item->calibration_params))
                                                        <code>{{ json_encode($item->calibration_params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</code>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if((int) $item->is_active === 1)
                                                        <span class="tw-badge tw-badge-green">{{ __('messages.stations.calibration.active') }}</span>
                                                    @else
                                                        <span class="tw-badge tw-badge-gray">{{ __('messages.stations.calibration.inactive') }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <button class="tw-btn-xs tw-btn-blue" wire:click="setStationCalibrationFormEdit({{ $item->id }})">
                                                        {{ __('messages.actions.edit') }}
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="py-6 text-center text-gray-500">{{ __('messages.stations.calibration.empty') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        @if(!$viewStationCalibrationForm && count($stationCalibrationAvailableVariables) === 0)
                            <p class="mb-0 text-xs text-gray-500">{{ __('messages.stations.calibration.messages.all_variables_taken') }}</p>
                        @endif

                        @if($viewStationCalibrationForm)
                            <div class="rounded border border-gray-200 mt-3 dark:border-gray-700">
                                <div class="border-b border-gray-200 px-4 py-2 dark:border-gray-700">
                                    <strong class="text-sm">
                                        @if($editStationCalibrationForm)
                                            {{ __('messages.stations.calibration.form.edit_title') }}
                                        @else
                                            {{ __('messages.stations.calibration.form.add_title') }}
                                        @endif
                                    </strong>
                                </div>
                                <div class="p-4">
                                    <div class="mb-3 grid grid-cols-1 gap-3 md:grid-cols-3">
                                        <div>
                                            <label class="tw-label">{{ __('messages.stations.calibration.form.variable') }}</label>
                                            <select class="tw-input" wire:model.live="stationCalibrationVariableName" @if($editStationCalibrationForm) disabled @endif>
                                                <option value="">{{ __('messages.common.select_option') }}</option>
                                                @foreach($stationCalibrationAvailableVariables as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="tw-label">{{ __('messages.stations.calibration.form.key') }}</label>
                                            <input type="text" class="tw-input" wire:model.live="stationCalibrationKey" placeholder="{{ __('messages.stations.calibration.form.key_placeholder') }}">
                                            <small class="text-xs text-gray-500">{{ __('messages.stations.calibration.form.key_help') }}</small>
                                        </div>
                                        <div>
                                            <label class="tw-label">{{ __('messages.stations.calibration.form.state') }}</label>
                                            <select class="tw-input" wire:model.live="stationCalibrationIsActive">
                                                <option value="1">{{ __('messages.stations.calibration.active') }}</option>
                                                <option value="0">{{ __('messages.stations.calibration.inactive') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="tw-label">{{ __('messages.stations.calibration.form.params') }}</label>
                                        <textarea class="tw-input" rows="4" wire:model.live="stationCalibrationParamsJson" placeholder="{{ __('messages.stations.calibration.form.params_placeholder') }}"></textarea>
                                        <small class="text-xs text-gray-500">{{ __('messages.stations.calibration.form.params_help') }}</small>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2 border-t border-gray-200 px-4 py-2 dark:border-gray-700">
                                    @if($editStationCalibrationForm)
                                        <button class="tw-btn tw-btn-blue" wire:click="updateStationCalibration">{{ __('messages.actions.update') }}</button>
                                    @else
                                        <button class="tw-btn tw-btn-emerald" wire:click="saveStationCalibration">{{ __('messages.actions.save') }}</button>
                                    @endif
                                    <button class="tw-btn tw-btn-gray" wire:click="closeStationCalibrationForm">{{ __('messages.actions.cancel') }}</button>
                                </div>
                            </div>
                        @endif
                    </div>

                    <x-slot:footer>
                        <button type="button" wire:click="closeStationCalibrationModal" class="tw-btn tw-btn-gray">
                            {{ __('messages.actions.close') }}
                        </button>
                    </x-slot:footer>
        </x-tw-modal>
        @endif

        {{-- Modal steelseries (Tailwind/Alpine) --}}
        <div x-data="{ open: false }" x-on:open-steelseries-modal.window="open = true" x-show="open" x-cloak
             x-on:keydown.escape.window="open = false"
             class="fixed inset-0 z-[60] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-black/50" x-on:click="open = false"></div>
            <div class="relative flex min-h-full items-start justify-center p-4 sm:p-6">
                <div class="relative w-full max-w-6xl rounded-lg bg-white shadow-xl dark:bg-gray-800">
                    <div class="flex items-center justify-between rounded-t-lg bg-gray-700 px-4 py-3 text-white">
                        <h5 class="m-0 text-base font-semibold">{{ __('messages.stations.modals.steelseries.title') }}</h5>
                        <button type="button" class="text-white/80 hover:text-white" x-on:click="open = false" aria-label="{{ __('messages.actions.close') }}"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="p-4">
                        <div class="rounded bg-red-600 px-3 py-2 text-white">{{ __('messages.stations.modals.steelseries.body_placeholder') }}</div>
                    </div>
                    <div class="flex justify-end gap-2 border-t border-gray-200 px-4 py-3 dark:border-gray-700">
                        <button type="button" class="tw-btn tw-btn-gray" x-on:click="open = false">{{ __('messages.actions.close') }}</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal de imagen (Tailwind/Alpine) --}}
        <div x-data="{ open: false, url: '', title: '' }"
             x-on:open-image-modal.window="open = true; url = $event.detail.url; title = $event.detail.title"
             x-show="open" x-cloak x-on:keydown.escape.window="open = false"
             class="fixed inset-0 z-[60] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-black/50" x-on:click="open = false"></div>
            <div class="relative flex min-h-full items-start justify-center p-4 sm:p-6">
                <div class="relative w-full max-w-lg rounded-lg bg-white shadow-xl dark:bg-gray-800">
                    <div class="flex items-center justify-between rounded-t-lg border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                        <h5 class="m-0 text-base font-semibold text-gray-800 dark:text-gray-100" x-text="title"></h5>
                        <button type="button" class="text-gray-500 hover:text-gray-700" x-on:click="open = false" aria-label="Close"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="p-4">
                        <img :src="url" alt="Imagen" class="w-full">
                    </div>
                    <div class="flex justify-end gap-2 border-t border-gray-200 px-4 py-3 dark:border-gray-700">
                        <button type="button" class="tw-btn tw-btn-gray" x-on:click="open = false">{{ __('messages.actions.close') }}</button>
                        <a class="tw-btn tw-btn-red" :href="url" :download="title">{{ __('messages.actions.download') }}</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Reusable modals --}}
        <livewire:viewer.forecast-modal />
        <livewire:viewer.alerts-modal />
        <livewire:viewer.station-info-modal />

    </div>

</div>
