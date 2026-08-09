@extends('layouts.admin')

@section('title', 'Importaciones')

@section('content_header')
    
    <!--h4>Estaciones</h4-->
@stop


@section('content')
<div class="mx-auto max-w-3xl px-4">
    <form method="POST" action="{{ route('import.user.data') }}" enctype="multipart/form-data">
        @csrf
        <div class="w-full">
            <div class="mb-3 flex">
                <label class="inline-flex cursor-pointer items-center rounded-l-md border border-emerald-500 bg-emerald-50 px-3 text-sm font-medium text-emerald-700" for="inputGroupFileInsert">Usuarios a insertar</label>
                <input type="file" class="tw-input flex-1 rounded-l-none" name="excel_file" id="inputGroupFileInsert" accept=".xlsx, .xls">
            </div>
            <div class="mt-3">
                <button type="submit" class="tw-btn tw-btn-blue">Importar e insertar</button>
            </div>
        </div>
    </form>
    <div class="my-4"></div>
    <form method="POST" action="{{ route('import.user.order.data') }}" enctype="multipart/form-data">
        @csrf
        <div class="w-full">
            <div class="mb-3 flex">
                <label class="inline-flex cursor-pointer items-center rounded-l-md border border-amber-500 bg-amber-50 px-3 text-sm font-medium text-amber-700" for="inputGroupFileOrder">Usuarios a ordenar</label>
                <input type="file" class="tw-input flex-1 rounded-l-none" name="excel_file_order" id="inputGroupFileOrder" accept=".xlsx, .xls">
            </div>
            <div class="mt-3">
                <button type="submit" class="tw-btn tw-btn-blue">Importar e ordenar</button>
            </div>
        </div>
    </form>
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

        .small-swal {
            width: 400px !important; 
            font-size: 14pt;
        }

        .small-icon {
            font-size: 25px !important; 
        } 
        
        .scroll-container {
            max-height: 70vh;
            overflow-y: auto;
            padding-right: 5px;
        }

        .accordion-horizontal {
            display: flex;
            overflow-x: auto;
            white-space: nowrap;
            width: 100%;
            padding-bottom: 20px;
        }

        /* Cada elemento del acordeón */
        .accordion-item {
            display: inline-block;
            min-width: 200px; /* Ancho mínimo de cada elemento */
            margin-right: 10px;
        }

        .accordion-item .card {
            width: 100%;
            display: flex;
            flex-direction: column;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .accordion-item .card-header {
            cursor: pointer;
            padding: 10px;
            text-align: center;
        }

        .accordion-item .card-body {
            display: none;
            padding: 10px;
            background-color: #f9f9f9;
        }

        /* Mostrar el body del acordeón cuando se expande */
        .accordion-item .collapse.show .card-body {
            display: block;
        }

        /* Si deseas un estilo de scroll, puedes agregar el siguiente código */
        .accordion-horizontal::-webkit-scrollbar {
            height: 8px;
        }

        .accordion-horizontal::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .accordion-horizontal::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
@stop
@section('footer')
    <div class="text-center">
    SenvaTec {{date('Y')}} | Desarrollado por <a href="https://www.artguz.com" style="color: #869099;">Artguz SRL</a> 
    </div>    
    <script type="module" src="{{ asset('js/steelseries.bundled.min.js') }}"></script>
    <!--script type="module" src="https://cdn.jsdelivr.net/npm/steelseries@2.0.7/dist/steelseries.bundled.min.js"></script-->
@stop
