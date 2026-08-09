@extends('layouts.admin')

@section('title', 'Pronósticos')

@section('content_header')    
    <h5>Pronósticos</h5>
@stop

@section('content')   
    <div>
        @livewire('forecasts.forecasts')        
    </div>    
@stop

@section('css')
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        background-color: #FFFFFF;
    }
    th, td {
        border: 1px solid black;
        padding: 3px;
        text-align: center;
        font-size: 10pt;
    }
    th {
        background-color: #f2f2f2;
    }
</style>
@stop

@section('footer')
<div class="text-center">
  SenvaTec {{date('Y')}} | Desarrollado por <a href="https://www.artguz.com" style="color: #869099;">Artguz SRL</a> 
</div>  
     
    <script> 

        function updateForecastJS(){
            let new_reg_dateAU = document.getElementById("new_reg_dateAU").value;
            let v10mAU = document.getElementById("v10mAU").value;
            let v10mdirAU = document.getElementById("v10mdirAU").value;
            let precAU = document.getElementById("precAU").value;
            let capeAU = document.getElementById("capeAU").value;
            let liAU = document.getElementById("liAU").value;
            let t2mAU = document.getElementById("t2mAU").value;
            let hr2mAU = document.getElementById("hr2mAU").value;
            let baroAU = document.getElementById("baroAU").value;
            let cloudAU = document.getElementById("cloudAU").value;
            let snowAU = document.getElementById("snowAU").value;
            let dpAU = document.getElementById("dpAU").value;
            
            let reg_dateAURegex = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/;
            if(new_reg_dateAU.trim() != ""){
                if (!reg_dateAURegex.test(new_reg_dateAU)) {
                    alert("El campo de fecha y hora no cumple con el formato requerido.");
                    return false;
                }
            }else{
                new_reg_dateAU = null;
            }
            
            if (isNaN(v10mAU) || v10mAU.trim() === "") {
                alert("El campo V10M debe ser un número válido.");
                return false;
            }

            /*if (isNaN(v10mdirAU) || v10mdirAU.trim() === "") {
                alert("El campo V10M DIR debe ser un número válido.");
                return false;
            }*/

            if (isNaN(precAU) || precAU.trim() === "") {
                alert("El campo PREC debe ser un número válido.");
                return false;
            }

            if (isNaN(capeAU) || capeAU.trim() === "") {
                alert("El campo CAPE debe ser un número válido.");
                return false;
            }

            if (isNaN(liAU) || liAU.trim() === "") {
                alert("El campo LI debe ser un número válido.");
                return false;
            }

            if (isNaN(t2mAU) || t2mAU.trim() === "") {
                alert("El campo T2M debe ser un número válido.");
                return false;
            }

            if (isNaN(hr2mAU) || hr2mAU.trim() === "") {
                alert("El campo HR2M debe ser un número válido.");
                return false;
            }

            if (isNaN(baroAU) || baroAU.trim() === "") {
                alert("El campo BARO debe ser un número válido.");
                return false;
            }

            if (isNaN(cloudAU) || cloudAU.trim() === "") {
                alert("El campo NUBES debe ser un número válido.");
                return false;
            }

            if (isNaN(snowAU) || snowAU.trim() === "") {
                alert("El campo NIEVE debe ser un número válido.");
                return false;
            }

            if (isNaN(dpAU) || dpAU.trim() === "") {
                alert("El campo DP debe ser un número válido.");
                return false;
            }  
            window.dispatchEvent(new CustomEvent('close-forecasts-modal'));
            Livewire.dispatch('updateForecast');
            
        }

        document.addEventListener('DOMContentLoaded', function () {

            /*let editableCells = document.querySelectorAll('.editable');
            editableCells.forEach(cell => {
                cell.addEventListener('blur', function() {
                    let newValue = cell.innerText; // Obtener el nuevo valor de la celda
                    let column = cell.getAttribute('data-column'); // Identificar la columna a actualizar
                    let rowId = cell.closest('tr').getAttribute('data-id'); // Obtener el id de la fila (registro)

                    // Emitir evento Livewire para actualizar el valor en el backend
                    Livewire.emit('updateCell', rowId, column, newValue);
                });
            });*/

            /*let editableCells = document.querySelectorAll('[contenteditable="true"]');
            editableCells.forEach(cell => {
                cell.addEventListener('blur', function() {
                    Console.log('-------------------------');
                    let newValue = cell.innerText; // Obtener el nuevo valor de la celda
                    let column = cell.getAttribute('data-column'); // Identificar la columna a actualizar
                    let rowId = cell.closest('tr').getAttribute('data-id'); // Obtener el id de la fila (registro)

                    
                    // Emitir evento Livewire para actualizar el valor en el backend
                    Livewire.dispatch('updateForecast', rowId, column, newValue);
                    //Livewire.emit('updateCell', rowId, column, newValue);
                });
            });*/

         let editableCells = document.querySelectorAll('[contenteditable="true"]');

        editableCells.forEach(cell => {
            // Añadir un evento 'blur' que se dispara cuando el usuario deja de editar
            cell.addEventListener('blur', function() {
                console.log('Evento blur activado en la celda:', cell);
                let newValue = cell.innerText; // Obtener el nuevo valor de la celda
                console.log('Nuevo valor:', newValue); // Imprimir el nuevo valor de la celda
                let column = cell.getAttribute('data-column'); // Identificar la columna
                let rowId = cell.closest('tr').getAttribute('data-id'); // Obtener el id de la fila

                // Verificar la información que se envía
                console.log('ID de la fila:', rowId);
                console.log('Columna:', column);
                console.log('Nuevo valor:', newValue);

                // Emitir evento Livewire (si lo tienes configurado)
                // Livewire.emit('updateCell', rowId, column, newValue);
            });
        });



            


            Livewire.on('showModal', () => {
                window.dispatchEvent(new CustomEvent('open-forecasts-modal'));
            });
        }); 
    </script>


    <script>
        function eliminarI(id){       
            Swal.fire({
                title: `<h5>¿Está seguro que desea eliminar el registro?</h5>`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: `Si, eliminar`,
                cancelButtonText: 'Cancelar',                
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('delete',[{ id: id}]);                    
                    /*Swal.fire(
                        'Eliminado!',
                        `correctamente.`,
                        'success'
                    )*/
                }
            })
        }

        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function() {
                $(this).remove();
            });
        }, 4000);


        function verifdDiscount(input) {
            input.value = input.value.replace(/\D/g, '');
            let valor = parseInt(input.value);
            if (Number.isInteger(valor) && valor >= 0 && valor <= 100) {
                
            } else {
                if (valor < 0) {
                    input.value = 0;
                } else if (valor > 100) {
                    input.value = 100;
                } else {
                    input.value = '';
                }
            }
        }
    </script> 
    
     
@stop
