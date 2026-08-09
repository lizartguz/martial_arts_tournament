@extends('layouts.admin')

@section('title', 'Estaciones')

@section('content_header')
    <!--h4>Estaciones</h4-->
@stop

@section('content')
    <div>
        @livewire('datamanager.resetdata')
    </div>
@stop

@section('css')
    <style type="text/css">
        #map {
        height: 370px;
        width: 100%;
        margin: 0px;
        padding: 0px;
        }

        #mapSation {
        height: 370px;
        width: 100%;
        margin: 0px;
        padding: 0px;
        }

        #mapE {
        height: 370px;
        width: 100%;
        margin: 0px;
        padding: 0px;
        }
        html,
        body {
        margin: 0;
        padding: 0;
        }
        .custom-map-control-button {
        appearance: button;
        background-color: #fff;
        border: 0;
        border-radius: 2px;
        box-shadow: 0 1px 4px -1px rgba(0, 0, 0, 0.3);
        cursor: pointer;
        margin: 0px;
        padding: 0 ;
        height: 40px;
        font: 400 18px Roboto, Arial, sans-serif;
        overflow: hidden;
        }
        .custom-map-control-button:hover {
        background: #0B0D8A;
        }
    </style>
@stop


@section('footer')
<div class="text-center">
  SenvaTec {{date('Y')}} | Desarrollado por <a href="https://www.artguz.com" style="color: #869099;">Artguz SRL</a>
</div>






@stop
