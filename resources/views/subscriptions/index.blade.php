@extends('layouts.admin')

@section('title', __('messages.subscription_types.page_title'))

@section('content_header')

    <h4>{{ __('messages.subscription_types.page_header') }}</h4>
@stop


@section('content')
   
    <div>

        @livewire('subscriptions.subscriptions')
        
    </div>
    
@stop

@section('css')
@stop

@section('footer')
<div class="text-center">
  SenvaTec {{date('Y')}} | Desarrollado por <a href="https://www.artguz.com" style="color: #869099;">Artguz SRL</a> 
</div> 


    
    
    <script>
        /*window.livewire.on('addResponsable', () => {
            $('#addResponsable').modal('hide');
            window.setTimeout(function() {
                $(".alert").fadeTo(500, 0).slideUp(500, function() {
                    $(this).remove();
                });
            }, 3000);
        });*/
        var detailsDataListDeleteU = [];
    </script>
   
    <script>
        function deleteSubsEvent(id){          
            console.log(1,id);
            Swal.fire({
                title: `<h5>¿Está seguro que desea eliminar el registro?</h5>`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: `Si, eliminar`,
                cancelButtonText: 'Cancelar',                
            }).then((result) => {
                console.log(2,id);
                if (result.isConfirmed) {
                    console.log(3,id,result.isConfirmed);
                    Livewire.dispatch('deleteSubs',[{ id: id}]);                    
                    /*Swal.fire(
                        'Eliminado!',
                        `correctamente.`,
                        'success'
                    )*/
                }
            })
        }
    </script>

    <script>
        let contadorInputs = 0;

        function agregarInput() {
            contadorInputs++;

            const nuevoInput = document.createElement('input');
            nuevoInput.type = 'text';
            nuevoInput.placeholder = 'Ingrese la información';
            nuevoInput.id = 'details_' + contadorInputs;
            nuevoInput.style.marginLeft = '5px';
            nuevoInput.maxLength = 80;
            nuevoInput.style.width = '80%';
            

            const botonEliminar = document.createElement('button');
            botonEliminar.textContent = 'Eliminar';
            botonEliminar.style.backgroundColor = 'red';
            botonEliminar.style.color = '#fff';
            botonEliminar.style.padding = '1px';
            botonEliminar.style.marginLeft = '5px';
            botonEliminar.onclick = function() {
                eliminarInput(nuevoInput.id);
            };

            const inputContainer = document.createElement('div');
            inputContainer.style.width = '100%';
            inputContainer.style.marginTop= '5px';
            //inputContainer.classList.add('input-container');
            inputContainer.appendChild(nuevoInput);
            inputContainer.appendChild(botonEliminar);

            document.getElementById('inputContainer').appendChild(inputContainer);
        }

        function eliminarInput(id) {
            const contenedor = document.getElementById(id).parentNode;
            contenedor.parentNode.removeChild(contenedor);
        }

    </script>


    <script>
        var contadorInputsU = 0;
        function agregarInputU() {
            contadorInputsU++;

            const nuevoInput = document.createElement('input');
            nuevoInput.type = 'text';
            nuevoInput.placeholder = 'Ingrese la información';
            nuevoInput.id = 'detailsU_' + contadorInputsU;
            nuevoInput.style.marginLeft = '5px';
            nuevoInput.maxLength = 80;
            nuevoInput.style.width = '80%';
            

            const botonEliminar = document.createElement('button');
            botonEliminar.textContent = 'Eliminar';
            botonEliminar.style.backgroundColor = 'red';
            botonEliminar.style.width = '13%';
            botonEliminar.style.color = '#fff';
            botonEliminar.style.padding = '1px';
            botonEliminar.style.marginLeft = '9px';
            botonEliminar.onclick = function() {
                eliminarInput(nuevoInput.id);
            };

            const inputContainer = document.createElement('div');
            inputContainer.style.width = '100%';
            inputContainer.style.marginTop= '5px';
            //inputContainer.classList.add('input-container');
            inputContainer.appendChild(nuevoInput);
            inputContainer.appendChild(botonEliminar);
            document.getElementById('inputContainerU').appendChild(inputContainer);
        }
        function eliminarInput(id) {
            const contenedor = document.getElementById(id).parentNode;
            contenedor.parentNode.removeChild(contenedor);
        }
        function deleteInputWithData(component,inputId,inputButton){
            const divElement = document.getElementById(component);
            const inputElement = document.getElementById(inputId);
            const botonElement = document.getElementById(inputButton);
            if (inputElement && botonElement) {
                divElement.parentNode.removeChild(divElement);
                inputElement.parentNode.removeChild(inputElement);
                botonElement.parentNode.removeChild(botonElement);
            }
            detailsDataListDeleteU.push(parseInt(inputId.match(/\d+/)[0]));
        }

    </script>

    <script>
        function verifPrice(){
            const inputPrice = document.getElementById("price");
            console.log(inputPrice.value);
            inputPrice.value = inputPrice.value.replace(/[^0-9.]/g, '');
            if (inputPrice.value.split('.').length > 2) {
                inputPrice.value = inputPrice.value.slice(0, -1);
            }
            var partes = inputPrice.value.split('.');
            if (partes[1] && partes[1].length > 2) {
                partes[1] = partes[1].slice(0, 2);
                inputPrice.value = partes.join('.');
            }
        }

        function verifPriceU(){
            const inputPriceU = document.getElementById("priceU");
            console.log(inputPriceU.value);
            inputPriceU.value = inputPriceU.value.replace(/[^0-9.]/g, '');
            if (inputPriceU.value.split('.').length > 2) {
                inputPriceU.value = inputPriceU.value.slice(0, -1);
            }
            var partes = inputPriceU.value.split('.');
            if (partes[1] && partes[1].length > 2) {
                partes[1] = partes[1].slice(0, 2);
                inputPriceU.value = partes.join('.');
            }
        }

        function verifDuration(){
            const inputDuration = document.getElementById("duration");
            inputDuration.value = inputDuration.value.replace(/[^0-9]/g, '');
            if (inputDuration.value.length > 3) {
                inputDuration.value = inputDuration.value.slice(0, -1);
            }
        }

        function verifDurationU(){
            const inputDurationU = document.getElementById("durationU");
            inputDurationU.value = inputDurationU.value.replace(/[^0-9]/g, '');
            if (inputDurationU.value.length > 3) {
                inputDurationU.value = inputDurationU.value.slice(0, -1);
            }
        }

        function verifdDurationDiscount(){
            const inputDurationDiscount = document.getElementById("duration_discount");
            inputDurationDiscount.value = inputDurationDiscount.value.replace(/[^0-9]/g, '');
            if (inputDurationDiscount.value.length > 3) {
                inputDurationDiscount.value = inputDurationDiscount.value.slice(0, -1);
            }
        }

        function verifdDurationDiscountU(){
            const inputDurationDiscount = document.getElementById("duration_discountU");
            inputDurationDiscount.value = inputDurationDiscount.value.replace(/[^0-9]/g, '');
            if (inputDurationDiscount.value.length > 3) {
                inputDurationDiscount.value = inputDurationDiscount.value.slice(0, -1);
            }
        }

        function verifValueDurationDiscount(){
            const inputDiscountValueDuration = document.getElementById("duration_value_discount");
            inputDiscountValueDuration.value = inputDiscountValueDuration.value.replace(/[^0-9]/g, '');
            if (inputDiscountValueDuration.value.length > 3) {
                inputDiscountValueDuration.value = inputDiscountValueDuration.value.slice(0, -1);
            }
        }

        function verifValueDurationDiscountU(){
            const inputDiscountValueDurationU = document.getElementById("duration_value_discountU");
            inputDiscountValueDurationU.value = inputDiscountValueDurationU.value.replace(/[^0-9]/g, '');
            if (inputDiscountValueDurationU.value.length > 3) {
                inputDiscountValueDurationU.value = inputDiscountValueDurationU.value.slice(0, -1);
            }
        }

        

        function verifAmount(){
            const inputAmount = document.getElementById("amount");
            inputAmount.value = inputAmount.value.replace(/[^0-9]/g, '');
            if (inputAmount.value.length > 3) {
                inputAmount.value = inputAmount.value.slice(0, -1);
            }
        }
        function verifAmountU(){
            const inputAmountU = document.getElementById("amountU");
            inputAmountU.value = inputAmountU.value.replace(/[^0-9]/g, '');
            if (inputAmountU.value.length > 3) {
                inputAmountU.value = inputAmountU.value.slice(0, -1);
            }
        }
        function verifDiscountValueStation(){
            const inputDiscountValueStation = document.getElementById("discount_value_stations");
            inputDiscountValueStation.value = inputDiscountValueStation.value.replace(/[^0-9]/g, '');
            if (inputDiscountValueStation.value.length > 3) {
                inputDiscountValueStation.value = inputDiscountValueStation.value.slice(0, -1);
            }
        }

        function verifDiscountValueStationU(){
            const inputDiscountValueStationU = document.getElementById("discount_value_stationsU");
            inputDiscountValueStationU.value = inputDiscountValueStationU.value.replace(/[^0-9]/g, '');
            if (inputDiscountValueStationU.value.length > 3) {
                inputDiscountValueStationU.value = inputDiscountValueStationU.value.slice(0, -1);
            }
        }
        function verifNumberStationsDiscount(){
            const inputNumberStationsDiscount = document.getElementById("number_stations_discount");
            inputNumberStationsDiscount.value = inputNumberStationsDiscount.value.replace(/[^0-9]/g, '');
            if (inputNumberStationsDiscount.value.length > 3) {
                inputNumberStationsDiscount.value = inputNumberStationsDiscount.value.slice(0, -1);
            }
        }

        function verifNumberStationsDiscountU(){
            const inputNumberStationsDiscountU = document.getElementById("number_stations_discountU");
            inputNumberStationsDiscountU.value = inputNumberStationsDiscountU.value.replace(/[^0-9]/g, '');
            if (inputNumberStationsDiscountU.value.length > 3) {
                inputNumberStationsDiscountU.value = inputNumberStationsDiscountU.value.slice(0, -1);
            }
        }
    </script>

    <script>
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function() {
                $(this).remove();
            });
        }, 4000);
    </script>

<script>
    function addSubscription(){
            const name = document.getElementById('name').value;
            const tag = document.getElementById('tag').value;
            const state = document.getElementById("state").value;
            const price =  document.getElementById('price').value;
            const duration =  document.getElementById('duration').value;
            const amount =  document.getElementById('amount').value;

            const duration_value_discount =  document.getElementById('duration_value_discount').value;
            const duration_discount =  document.getElementById('duration_discount').value;
            const stateDE = document.getElementById("stateDE").value;
            
            const discount_value_stations =  document.getElementById('discount_value_stations').value; 
            const number_stations_discount =  document.getElementById('number_stations_discount').value;                      
            const stateDD = document.getElementById("stateDD").value;            

            
           
            if (name.trim() == '') {
                alert('Debe agregar un título para la suscripción.');
            }else if(name.length > 40){
                alert('El título o descripción, no debe sobrepasar los 40 caracteres');
            }else if (tag.trim() == '') {
                alert('Debe agregar una etiqueta identificativa para la suscripción.');
            }else if(tag.length > 40){
                alert('La etiqueta identificativa, no debe sobrepasar los 40 caracteres');
            }else if(price.length < 0){
                alert('Debe agregar un precio');
            }else if (price == '.') {
                alert('Debe agregar un valor correcto para el precio de la suscripción.');
            }else if (duration.length < 0) {
                alert('Debe agregar una duración  mayor a cero para la suscripción.');

            }else if(isNaN(duration_discount)){
                alert('Debe agregar un tiempo de duración correcto para el descuento por duración a la suscripción.');
            }else if(duration_discount.length < 0){
                alert('Debe agregar un tiempo de duración mayor o igual a uno para el descuento por duración');         
            }else if(isNaN(duration_value_discount)){
                alert('Debe agregar un porcentaje de descuento correcto por tiempo de duración para la suscripción.');
            }else if(duration_value_discount.length < 0){
                alert('Debe agregar un porcentaje de descuento mayor o igual a cero para la duraci+on de la suscripción');   
            }else if(isNaN(discount_value_stations)){
                alert('Debe agregar un porcentaje de descuento correcto por cantidad de estaciones para la suscripción.');
            }else if(discount_value_stations.length < 0){
                alert('Debe agregar un porcentaje de descuento mayor o igual a cero para la cantidad de estaciones');
            }else if(number_stations_discount.length < 0){
                alert('Debe agregar un valor numérico en el campo: "Nro de estaciones para descuento"');
            }else if (amount.length < 1) {
                alert('Debe agregar una cantidad de suscriptores mayor a cero para la suscripción.');
            }else if(state.length < 0){
                alert('No tiene ninguna estado seleccionado.');
            }else if(stateDE.length < 0){
                alert('No tiene ningun estado seleccionado para el descuento poo estación.');
            }else if(stateDD.length < 0){
                alert('No tiene ningun estado seleccionado para el descuento por estación.');
            }else{
                Livewire.dispatch('addSubscription',[{ name: name, tag:tag, state: state, amount: amount, price: price, duration: duration, duration_discount: duration_discount, discount_value_stations: discount_value_stations, number_stations_discount: number_stations_discount,duration_value_discount:duration_value_discount, stateDE: stateDE, stateDD: stateDD,}]);
            }
        
             
    }
</script>

<script>

    function updateSubscription(){
          //alert('aaaaaaa');
          const nameU = document.getElementById('nameU').value;
          const tagU = document.getElementById('tagU').value;
          const stateU = document.getElementById("stateU").value;
          const priceU =  document.getElementById('priceU').value;
          const amountU = document.getElementById('amountU').value;
          const durationU =  document.getElementById('durationU').value;      
          
          const duration_value_discountU =  document.getElementById('duration_value_discountU').value;
          const duration_discountU =  document.getElementById('duration_discountU').value;
          const stateDEU = document.getElementById("stateDEU").value;
          
          const discount_value_stationsU =  document.getElementById('discount_value_stationsU').value; 
          const number_stations_discountU =  document.getElementById('number_stations_discountU').value;                      
          const stateDDU = document.getElementById("stateDDU").value;
         
        if (nameU.trim() == '') {
            alert('Debe agregar un título para la suscripción.');
        }else if(nameU.length > 40){
            alert('El título o descripción, no debe sobrepasar los 40 caracteres');
        }else if (tagU.trim() == '') {
            alert('Debe agregar una etiqueta identificativa para la suscripción.');
        }else if(tagU.length > 40){
            alert('La etiqueta identificativa, no debe sobrepasar los 40 caracteres');
        }else if(priceU.length < 0){
            alert('Debe agregar un precio');
        }else if (priceU == '.') {
            alert('Debe agregar un valor correcto para el precio de la suscripción.');
        }else if (durationU.length < 0) {
            alert('Debe agregar una duración  mayor a cero para la suscripción.');

        }else if(isNaN(duration_discountU)){
            alert('Debe agregar un tiempo de duración correcto para el descuento por duración a la suscripción.');
        }else if(duration_discountU.length < 0){
            alert('Debe agregar un tiempo de duración mayor o igual a uno para el descuento por duración');         
        }else if(isNaN(duration_value_discountU)){
            alert('Debe agregar un porcentaje de descuento correcto por tiempo de duración para la suscripción.');
        }else if(duration_value_discountU.length < 0){
            alert('Debe agregar un porcentaje de descuento mayor o igual a cero para la duraci+on de la suscripción');


        }else if(isNaN(discount_value_stationsU)){
            alert('Debe agregar un porcentaje de descuento correcto por cantidad de estaciones para la suscripción.');
        }else if(discount_value_stationsU.length < 0){
            alert('Debe agregar un porcentaje de descuento mayor o igual a cero para la cantidad de estaciones');
        }else if(number_stations_discountU.length < 0){
            alert('Debe agregar un valor numérico en el campo: "Nro de estaciones para descuento"');
        }else if (amountU.length < 1) {
            alert('Debe agregar una cantidad de suscriptores mayor a cero para la suscripción.');
        }else if(stateU.length < 0){
            alert('No tiene ningun estado seleccionado para la suscripción.');
        }else if(stateDEU.length < 0){
            alert('No tiene ningun estado seleccionado para el descuento poo estación.');
        }else if(stateDDU.length < 0){
            alert('No tiene ningun estado seleccionado para el descuento por estación.');
        }else{
            Livewire.dispatch('updateSubscription',[{ nameU: nameU,tagU:tagU, stateU: stateU, amountU: amountU, priceU: priceU, durationU: durationU, duration_discountU: duration_discountU, discount_value_stationsU: discount_value_stationsU, number_stations_discountU: number_stations_discountU,
            duration_value_discountU:duration_value_discountU, stateDEU: stateDEU, stateDDU: stateDDU,}]);
        }
      }
</script>

@stop
