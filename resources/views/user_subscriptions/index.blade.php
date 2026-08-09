@extends('layouts.admin')

@section('title', __('messages.subscriptions.page.title'))

@section('content_header')
    
    <h5>{{ __('messages.subscriptions.page.header') }}</h5>
@stop


@section('content')
   
    <div>
        <livewire:users_subscriptions.users-subscriptions />
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

        .password-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .password-containerE {
            position: relative;
        }

        .password-toggleE {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }

        /*OCULTAR DIGITOS*/
        .blurred {
            filter: blur(6px);
            cursor: pointer;
            transition: filter 0.3s ease;
            user-select: none;
        }

        .blurred.revealed {
            filter: none;
            font-weight: bold;
        }
    </style>
@stop
@section('js')

    
    <script>
        //const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
        //const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))
        /*window.livewire.on('addResponsable', () => {
            $('#addResponsable').modal('hide');
            window.setTimeout(function() {
                $(".alert").fadeTo(500, 0).slideUp(500, function() {
                    $(this).remove();
                });
            }, 3000);
        });*/       
       
        var detailsDataListDeleteU = [];
        const subscriptionsPageTranslations = {
            buttons: {
                close: @json(__('messages.subscriptions.page.buttons.close')),
                cancel: @json(__('messages.subscriptions.page.buttons.cancel')),
                ok: @json(__('messages.subscriptions.page.buttons.ok')),
                yesDelete: @json(__('messages.subscriptions.page.buttons.yes_delete')),
                yesUnlink: @json(__('messages.subscriptions.page.buttons.yes_unlink')),
                yesChange: @json(__('messages.subscriptions.page.buttons.yes_change')),
                yesLink: @json(__('messages.subscriptions.page.buttons.yes_link')),
                yesConfirm: @json(__('messages.subscriptions.page.buttons.yes_confirm')),
            },
            messages: {
                stationOptionRequired: @json(__('messages.subscriptions.page.messages.station_option_required')),
                stationRequiredChange: @json(__('messages.subscriptions.page.messages.station_required_change')),
                stationRequiredLink: @json(__('messages.subscriptions.page.messages.station_required_link')),
                stationRequiredNewLink: @json(__('messages.subscriptions.page.messages.station_required_new_link')),
            },
            confirm: {
                deleteRecord: @json(__('messages.subscriptions.page.confirm.delete_record')),
                unlinkStation: @json(__('messages.subscriptions.page.confirm.unlink_station')),
                changeStationLink: @json(__('messages.subscriptions.page.confirm.change_station_link')),
                linkStation: @json(__('messages.subscriptions.page.confirm.link_station')),
                changeStation: @json(__('messages.subscriptions.page.confirm.change_station')),
                resetDevice: @json(__('messages.subscriptions.page.confirm.reset_device')),
                changeUserStatus: @json(__('messages.subscriptions.page.confirm.change_user_status')),
            },
            map: {
                markerTitle: @json(__('messages.subscriptions.page.map.marker_title')),
            },
        };
    </script>

   


<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}" async defer>
</script>

<script>
        function messageWithStation(){
          let a = document.getElementById('flexRadioDefault1');
          let b = document.getElementById('flexRadioDefault2');
          a.checked = true;
          b.checked = false;
          Swal.fire({
                title: `<h5>${subscriptionsPageTranslations.messages.stationOptionRequired}</h5>`,              
                confirmButtonColor: '#D63E30',               
                confirmButtonText: subscriptionsPageTranslations.buttons.close,                
            });          
        }



        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            passwordInput.type = (passwordInput.type === 'password') ? 'text' : 'password';
        }

        /*document.getElementById('togglePassword').addEventListener('click', function (e) {
            const passwordField = document.getElementById('passwordOwnerE');
            const eyeIcon = document.getElementById('eyeIcon');
            
            // Cambiar el tipo de input
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash'); 
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });*/

        function togglePasswordVisibilityE() {
            const passwordInput = document.getElementById('passwordE');
            passwordInput.type = (passwordInput.type === 'password') ? 'text' : 'password';
        }

        function togglePasswordVisibilityOwnerE() {
            const passwordInput = document.getElementById('passwordOwnerE');
            passwordInput.type = (passwordInput.type === 'password') ? 'text' : 'password';
        }

        const labels = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";        
        let labelIndex = 0;       
        var array=new Array();
        var latitud="";
        var longitud="";       
        var latitudE="";
        var longitudE="";
        var coord;
        var myLatlng;
        var map; 
                   
        async function initMap(type,lat,lng,zoom) { 
            console.log('initMap: ',type,lat,lng,zoom);
            coord = { lat: parseFloat(lat), lng: parseFloat(lng) };
            myLatlng = new google.maps.LatLng(lat,lng);
            const { Map } = await google.maps.importLibrary("maps");
            const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
            map = new google.maps.Map(document.getElementById('map'), {
                center: coord,
                zoom: zoom,
                mapId:"def05be188adae9d",
            });  
            const marker = new AdvancedMarkerElement({ position: myLatlng,title: subscriptionsPageTranslations.map.markerTitle,});
            array[0] = marker;  
            array[0].setMap(map);
            google.maps.event.addListener(map,'click',function(event) {                
                addMarker(event.latLng.lat().toString(),event.latLng.lng().toString(), map, type);
            });
        }
       
        async function addMarker(lat,lng, map, type) {
            const position = { lat: parseFloat(lat), lng: parseFloat(lng) };        
            const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
            const markerInst = new AdvancedMarkerElement({
                position:position,                         
                draggable:false,
                title: subscriptionsPageTranslations.map.markerTitle,
            });
            array[0].setMap(null);
            array[0] = markerInst;
            array[0].setMap(map);
            //document.getElementById("latitudE").value=lat;
            //document.getElementById("longitudE").value=lng;
            //console.log(document.getElementById("latitud").value);
            //console.log(document.getElementById("longitud").value);
            Livewire.dispatch('setCoordinates',[{ lat:lat, lng:lng}]);
            
        }        

        Livewire.on('changeSubscription',(event) =>{
            console.log('changeSubscription: ',event[0].type, event[0].latitud, event[0].longitud);
            initMap(event[0].type, event[0].latitud, event[0].longitud,5);
        });

        Livewire.on('openMapE',(event) =>{
            initMap(event[0].type, event[0].latitudE, event[0].longitudE,14);
        });

        Livewire.on('changeSubscriptionStation',(event) =>{  
            const status = event[0].selectStationStatus;
            const zoom = status==true?14:5;                    
            initMap(event[0].type, event[0].latitud, event[0].longitud,zoom);
            document.getElementById('flexRadioDefault1').checked = status==true?false:true;
            document.getElementById('flexRadioDefault2').checked = status==true?true:false;
        });
    </script> 
    
    <script>
        function eliminarI(id){          
            Swal.fire({
                title: `<h5>${subscriptionsPageTranslations.confirm.deleteRecord}</h5>`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: subscriptionsPageTranslations.buttons.yesDelete,
                cancelButtonText: subscriptionsPageTranslations.buttons.cancel,                
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('delete',[{ id: id}]);            
                }
            })
        }

        window.deleteStationManager = function (data) {  
            console.log('deleteStationManager:', data);          
            Swal.fire({
                title: `<h5>${subscriptionsPageTranslations.confirm.unlinkStation}</h5>`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: subscriptionsPageTranslations.buttons.yesUnlink,
                cancelButtonText: subscriptionsPageTranslations.buttons.cancel,
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('deleteStationManager', { data: data });
                }
            });
        };

        window.changeStationManager = function (data) {  
            console.log('changeStationManager:', data);          
            Swal.fire({
                title: `<h5>${subscriptionsPageTranslations.confirm.changeStationLink}</h5>`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: subscriptionsPageTranslations.buttons.yesChange,
            }).then((result) => {
                if (result.isConfirmed) {
                    let stationName = document.getElementById('chooseStationListUpdateOwnerName')?.value || '';
                    if (!stationName.trim()) {
                        Swal.fire({
                            title: `<h5>${subscriptionsPageTranslations.messages.stationRequiredChange}</h5>`,
                            confirmButtonColor: '#7A0D0DF6',                                
                            confirmButtonText: subscriptionsPageTranslations.buttons.ok,                
                        });
                    } else {
                        Livewire.dispatch('changeStationManager', { data: data });
                    }            
                }
            });
        };

        window.addStationManager = function (data) {  
            console.log('addStationManager:', data);              
            Swal.fire({
                title: `<h5>${subscriptionsPageTranslations.confirm.linkStation}</h5>`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: subscriptionsPageTranslations.buttons.yesLink,
                cancelButtonText: subscriptionsPageTranslations.buttons.cancel,                
            }).then((result) => {
                if (result.isConfirmed) {
                    let stationName = document.getElementById('chooseStationListUpdateOwnerName')?.value || '';
                    if (!stationName.trim()) {
                        Swal.fire({
                            title: `<h5>${subscriptionsPageTranslations.messages.stationRequiredLink}</h5>`,
                            confirmButtonColor: '#7A0D0DF6',                                
                            confirmButtonText: subscriptionsPageTranslations.buttons.ok,
                        });
                    } else {
                        Livewire.dispatch('addStationManager', { data: data });
                    }            
                }
            });
        };


        window.unLinkStationE  = function () {
            Swal.fire({
                title: `<h5>${subscriptionsPageTranslations.confirm.unlinkStation}</h5>`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: subscriptionsPageTranslations.buttons.yesUnlink,
                cancelButtonText: subscriptionsPageTranslations.buttons.cancel,                
            }).then((result) => {
                if (result.isConfirmed) {
                   Livewire.dispatch('unLinkStationE');
                }
            });
        };

        window.changeStationE  = function () {
            Swal.fire({
                title: `<h5>${subscriptionsPageTranslations.confirm.changeStation}</h5>`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: subscriptionsPageTranslations.buttons.yesLink,
                cancelButtonText: subscriptionsPageTranslations.buttons.cancel,                
            }).then((result) => {
                if (result.isConfirmed) {
                    let stationName = document.getElementById('stationNameNewE')?.value || '';
                    if (!stationName.trim()) {
                        Swal.fire({
                            title: `<h5>${subscriptionsPageTranslations.messages.stationRequiredNewLink}</h5>`,
                            confirmButtonColor: '#7A0D0DF6',                                
                            confirmButtonText: subscriptionsPageTranslations.buttons.ok,
                        });
                    } else {
                        Livewire.dispatch('changeStationE');
                    }            
                }
            });
        };

        function deleteStationManager(subscritionId,userId,stationId){          
            Swal.fire({
                title: `<h5>${subscriptionsPageTranslations.confirm.unlinkStation}</h5>`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: subscriptionsPageTranslations.buttons.yesDelete,
                cancelButtonText: subscriptionsPageTranslations.buttons.cancel,                
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('deleteStationManager',[{ subscritionId:subscritionId, userId:userId, stationId:stationId}]);            
                }
            })
        }

        function resetDevice(id){          
            Swal.fire({
                title: `<h5>${subscriptionsPageTranslations.confirm.resetDevice}</h5>`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: subscriptionsPageTranslations.buttons.yesDelete,
                cancelButtonText: subscriptionsPageTranslations.buttons.cancel,                
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('resetDevice',[{ userId: id}]);            
                }
            })
        }

        function changeStatusUser(actualState,userId,position,type){   
            console.log(actualState,userId,position,type);       
            Swal.fire({
                title: `<h5>${subscriptionsPageTranslations.confirm.changeUserStatus}</h5>`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: subscriptionsPageTranslations.buttons.yesConfirm,
                cancelButtonText: subscriptionsPageTranslations.buttons.cancel,                
            }).then((result) => {
                if (result.isConfirmed) {
                        Livewire.dispatch('changeStatusUser',[{actualState: actualState, userId: userId, position: position, type: type}]);            
                    
                }
            })                    
        }
    </script>

    <script>
        function verifDiscountInvitate(){
            Livewire.dispatch('verifDiscountInvitate');
        }

        function deleteDiscountInvitate(){
            Livewire.dispatch('deleteDiscountInvitate');
        }

        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function() {
                $(this).remove();
            });
        }, 4000);
    </script>

    <script>
        function reveal(element) {
        element.classList.toggle('revealed');
    }
    </script>
@stop


@section('footer')
<div class="text-center">
  SenvaTec {{date('Y')}} | {{ __('messages.subscriptions.page.footer.developed_by') }} <a href="https://www.artguz.com" style="color: #869099;">Artguz SRL</a>
</div>
@stop
