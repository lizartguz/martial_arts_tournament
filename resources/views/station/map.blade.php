@extends('layouts.admin')

@section('title', __('messages.stations.map.page.title'))

@section('css')
    <link rel="stylesheet" href="{{ asset('css/station-map.css') }}?v=20260619">
@stop

@section('content_header')
    <h4>{{ __('messages.stations.map.page.header') }}</h4>
@stop

@section('content')
    @livewire('station.station-map',['typeS' => $typeS])
@stop

@section('js')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}" async defer></script>
<script>
    window.stationMapTranslations = {
        infoWindow: {
            buttons: {
                moreData: @json(__('messages.stations.map.info_window.buttons.more_data')),
                viewAll: @json(__('messages.stations.map.info_window.buttons.view_all')),
            },
            sections: {
                agriculturalSensors: @json(__('messages.stations.map.info_window.sections.agricultural_sensors')),
                aviationVariables: @json(__('messages.stations.map.info_window.sections.aviation_variables')),
            },
            labels: {
                temperature: @json(__('messages.stations.map.info_window.labels.temperature')),
                humidity: @json(__('messages.stations.map.info_window.labels.humidity')),
                dewPoint: @json(__('messages.stations.map.info_window.labels.dew_point')),
                windDirection: @json(__('messages.stations.map.info_window.labels.wind_direction')),
                windSpeed: @json(__('messages.stations.map.info_window.labels.wind_speed')),
                gust: @json(__('messages.stations.map.info_window.labels.gust')),
                precipitation: @json(__('messages.stations.map.info_window.labels.precipitation')),
                intensity: @json(__('messages.stations.map.info_window.labels.intensity')),
                barometricPressure: @json(__('messages.stations.map.info_window.labels.barometric_pressure')),
                windDirection2: @json(__('messages.stations.map.info_window.labels.wind_direction_2')),
                windSpeed2: @json(__('messages.stations.map.info_window.labels.wind_speed_2')),
                gust2: @json(__('messages.stations.map.info_window.labels.gust_2')),
                solarRadiation: @json(__('messages.stations.map.info_window.labels.solar_radiation')),
                evapotranspiration: @json(__('messages.stations.map.info_window.labels.evapotranspiration')),
                soilTemp1: @json(__('messages.stations.map.info_window.labels.soil_temp_1')),
                soilTemp2: @json(__('messages.stations.map.info_window.labels.soil_temp_2')),
                soilHum1: @json(__('messages.stations.map.info_window.labels.soil_hum_1')),
                soilHum2: @json(__('messages.stations.map.info_window.labels.soil_hum_2')),
                leafTemp1: @json(__('messages.stations.map.info_window.labels.leaf_temp_1')),
                leafTemp2: @json(__('messages.stations.map.info_window.labels.leaf_temp_2')),
                leafHum1: @json(__('messages.stations.map.info_window.labels.leaf_hum_1')),
                leafHum2: @json(__('messages.stations.map.info_window.labels.leaf_hum_2')),
                qnh: @json(__('messages.stations.map.info_window.labels.qnh')),
            },
            units: {
                evapotranspiration: @json(__('messages.stations.map.info_window.units.evapotranspiration')),
            },
        },
        actions: {
            close: @json(__('messages.actions.close')),
        },
        markers: {
            title: @json(__('messages.stations.map.markers.title')),
        },
    };
</script>
<script src="{{ asset('js/station-map-aviation.js') }}?v=20260619"></script>
<script src="{{ asset('js/station-map-info-window.js') }}?v=20260619"></script>
<script src="{{ asset('js/station-map.js') }}?v=20260619"></script>
@stop

@section('footer')
    <div class="text-center">
        SenvaTec {{date('Y')}} | Desarrollado por <a href="https://www.artguz.com" style="color: #869099;">Artguz SRL</a> 
    </div>    
@stop
