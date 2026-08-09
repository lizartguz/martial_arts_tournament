@extends('layouts.admin')

@section('title', __('messages.stations.page_title'))

@section('content')
    <div>
         @livewire('station.station',['typeS' => $typeS])
    </div>
@stop

@section('css')
    <style type="text/css">
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


@section('js')

<script>
        function viewAndDownloadImage(title, url) {
            window.dispatchEvent(new CustomEvent('open-image-modal', { detail: { title: title, url: url } }));
        }
</script>



<script>



    Livewire.on("showSteelSeriesModal", () => {
        window.dispatchEvent(new CustomEvent('open-steelseries-modal'));
    });

    function openModal() {
        window.dispatchEvent(new CustomEvent('open-steelseries-modal'));
    }



    function validateImageConsole(input) {
        const allowedTypes = ['image/png', 'image/jpeg', 'image/webp'];
        const file = input.files[0];
        if (file) {
            if (allowedTypes.includes(file.type)) {
            document.getElementById('errorText').innerText = '';
            } else {
            document.getElementById('errorText').innerText = 'Formato de imagen no válido. Por favor, selecciona una imagen PNG, JPG, o WEBP.';
            input.value = '';
            }
        }
    }


    function addStation(){
        let lat = document.getElementById('latitudeAU').value;
        let lng = document.getElementById('longitudeAU').value;

        let failedAlert = "";

        const datos = {
            nameAU: document.getElementById('nameAU')?.value || null,
            municipalityIdAU: document.getElementById('municipalityIdAU')?.value || null,
            latitudeAU: document.getElementById('latitudeAU')?.value || null,
            longitudeAU: document.getElementById('longitudeAU')?.value || null,
            elevationDT: document.getElementById('elevationDT')?.value || null,
            power_supplyDT: document.getElementById('power_supplyDT')?.value || null,
            brandIdDT: document.getElementById('brandIdDT')?.value || null,
            stationModelIdDT: document.getElementById('stationModelIdDT')?.value || null,
            installation_dateDT: document.getElementById('installation_dateDT')?.value || null,
        };

        if (!datos.nameAU) {
            failedAlert = "Debe agregar un <strong>nombre</strong> para la estación.<br> En la sección de 'Datos de estación'";
        } else if (datos.municipalityIdAU === "-1" || datos.municipalityIdAU == null) {
            failedAlert = "Debe seleccionar un <strong>municipio</strong><br> para la sección de 'Datos de estación'";
        } else if (!datos.elevationDT) {
            failedAlert = "Debe agregar una <strong>elevación(mts)</strong><br> para la sección de 'Datos de estación'";
        } else if (datos.power_supplyDT === "-1" || datos.power_supplyDT == null) {
            failedAlert = "Debe seleccionar una <strong>Fuente de energia</strong><br> para la sección de 'Datos de estación'";
        } else if (datos.brandIdDT === "-1" || datos.brandIdDT == null) {
            failedAlert = "Debe seleccionar una <strong>marca</strong><br> para la sección de 'Datos de estación'";
        } else if (datos.stationModelIdDT === "-1" || datos.stationModelIdDT == null) {
            failedAlert = "Debe seleccionar un <strong>modelo</strong><br> para la sección de 'Datos de estación'";
        } else if (!datos.latitudeAU) {
            failedAlert = "Debe agregar una <strong>latitud</strong> o seleccionar desde el mapa<br> para la sección de 'Datos de estación'";
        } else if (!datos.longitudeAU) {
            failedAlert = "Debe agregar una <strong>longitud</strong> o seleccionar desde el mapa<br> para la sección de 'Datos de estación'";
        } else if (!datos.installation_dateDT) {
            failedAlert = "Debe agregar o seleccionar una <strong>fecha de instalación</strong><br> para la sección de 'Datos de estación'";
        }

        if (failedAlert) {
            /*Swal.fire({
                icon: 'error',
                title: 'Error en la validación',
                html: failedAlert
            });*/
            Swal.fire({
                icon: 'error',
                title: 'Error en la validación',
                html: failedAlert,
                customClass: {
                    popup: 'small-swal',
                    icon: 'small-icon'
                }
            });                
        } else {

            Livewire.dispatch('addStation',[{lat:lat, lng: lng,}]);
        }

        
    }

    function confirmDelete(stationId) {
        Swal.fire({
            title: '<span style="font-size: 14pt;">Ingrese el ID:'+stationId+ ' de la estación para poder eliminarla</span>',
            input: "text",
            inputAttributes: {
                autocapitalize: "off"
            },
            showCancelButton: true,
            confirmButtonText: "Confirmar",
            cancelButtonText: "Cancelar",
            showLoaderOnConfirm: true,
            preConfirm: (id) => {
                if (!id) {
                    Swal.showValidationMessage("El ID de estación es requerido");
                    return false;
                }else if (id!=stationId){
                    Swal.showValidationMessage("El ID de estación es incorrecto");
                    return false;
                }else{
                    Livewire.dispatch('deleteStation',[{'id':stationId}]);                        
                }                  
                
            }
        });
    }

    function updateStation(){
        let lat = document.getElementById('latitudeAU').value;
        let lng = document.getElementById('longitudeAU').value;
        Livewire.dispatch('updateStation',[{lat:lat, lng: lng,}]);
    }


    


</script>

<script>
    var canvas;
    var signaturePad;

    Livewire.on('set-signature-pad',(event) =>{     
        canvas = document.getElementById('signature-pad');            
        if(canvas){
            signaturePad = new SignaturePad(canvas, {
                minWidth: 2,
                backgroundColor: 'white',
                maxWidth: 2,
                penColor: "rgb(66, 133, 244)"
            });                              
        }                                
    });

    function signatureErase(){
        signaturePad.clear();       
    }

    function registerFormMaintanancePlan(){    
        if (signaturePad.isEmpty()) {
            Swal.fire({
                title: '<h5>'+ 'Debe agregar la Firma gráfica'+'</h5>',              
                confirmButtonColor: '#D63E30',               
                confirmButtonText: 'Cerrar',                
            });
        }else{
            const data = signaturePad.toDataURL();
            Livewire.dispatch('registerFormMaintanancePlan',[{signatureData:data}]); 
        }      
    }
</script>

<script>
function generateChartData() {
    const labels = [];
    const data = [];
    let currentDate = new Date('2025-04-01T00:00:01'); // Fecha inicial
    let temperature = 17.9; // Temperatura inicial

    for (let i = 0; i < 60; i++) {
        labels.push(currentDate.toISOString().replace('T', ' ').split('.')[0]); // Formato de texto
        data.push(parseFloat(temperature.toFixed(3)));

        // Simular variaciones de temperatura
        temperature += (Math.random() - 0.5) * 0.2;
        currentDate.setMinutes(currentDate.getMinutes() + 1);
    }

    return { labels, data };
}

function resetZoom() {
    let canvas = document.getElementById('graph');
    if (window.myChart) {
        window.myChart.resetZoom();
    }
}

function downloadImage(format) {
    const canvas = document.getElementById('graph');
    const tempCanvas = document.createElement('canvas');
    const ctx = tempCanvas.getContext('2d');

    // Ajustar tamaño del nuevo canvas
    tempCanvas.width = canvas.width;
    tempCanvas.height = canvas.height;

    // Rellenar con fondo blanco
    ctx.fillStyle = '#FFFFFF';
    ctx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);

    // Dibujar el gráfico original encima
    ctx.drawImage(canvas, 0, 0);

    // Convertir a imagen y descargar
    const link = document.createElement('a');
    link.href = tempCanvas.toDataURL(`image/${format}`);
    link.download = `chart.${format}`;
    link.click();
}

function downloadPDF() {
    const canvas = document.getElementById('graph');
    const tempCanvas = document.createElement('canvas');
    const ctx = tempCanvas.getContext('2d');

    // Ajustar tamaño
    tempCanvas.width = canvas.width;
    tempCanvas.height = canvas.height;

    // Fondo blanco
    ctx.fillStyle = '#FFFFFF';
    ctx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);
    ctx.drawImage(canvas, 0, 0);

    // Convertir a imagen
    const canvasImage = tempCanvas.toDataURL('image/jpeg', 1.0);
    
    let pdf = new jspdf.jsPDF({ orientation: 'landscape' });
    pdf.setFontSize(20);
    pdf.addImage(canvasImage, 'jpeg', 15, 15, 260, 140);
    pdf.save('chart.pdf');
}

Livewire.on("updateChart", (chartData) => {
    setTimeout(() => {
        let canvas = document.getElementById('graph');
        if (canvas) {
            let ctx = canvas.getContext('2d');
            if (ctx) {
                if (window.myChart) {
                    window.myChart.destroy();
                }
                window.myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData[0].labels,
                        datasets: [{
                            label: chartData[0].title,
                            data: chartData[0].data,
                            borderColor: "red",
                            borderWidth: 2,
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                type: 'category', // Sin necesidad de librerías extras
                                ticks: {
                                    autoSkip: true,
                                    maxTicksLimit: 10
                                }
                            }
                        },
                        plugins: {
                            zoom: {
                                zoom: {
                                    wheel: {
                                        enabled: true,
                                    },
                                    pinch: {
                                        enabled: true
                                    },
                                    mode: 'x',
                                },
                                pan: {
                                    enabled: true,
                                    mode: 'x',
                                }
                            }
                        }
                    }
                });
            }
        }
    }, 1000);

});
</script>

{{-- Mapa interactivo para seleccionar la ubicacion de la estacion (form agregar/editar) --}}
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}"></script>
<script>
    (function () {
        const STATION_FORM_MAP_DEFAULT = { lat: -17.784697, lng: -63.182028 };
        let stationFormMap = null;
        let stationFormMarker = null;

        // Refleja la ubicacion seleccionada en los inputs y la sincroniza con Livewire.
        function syncStationFormLatLng(lat, lng) {
            const latInput = document.getElementById('latitudeAU');
            const lngInput = document.getElementById('longitudeAU');
            if (latInput) {
                latInput.value = lat;
                latInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
            if (lngInput) {
                lngInput.value = lng;
                lngInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }

        // Crea o reposiciona el unico marcador del mapa.
        function setStationFormMarker(position) {
            if (stationFormMarker) {
                stationFormMarker.setPosition(position);
            } else {
                stationFormMarker = new google.maps.Marker({
                    position: position,
                    map: stationFormMap,
                });
            }
        }

        function initStationFormMap(lat, lng) {
            const el = document.getElementById('stationFormMap');
            if (!el || !(window.google && window.google.maps)) {
                return false;
            }

            const hasCoords = !isNaN(lat) && !isNaN(lng);
            const center = hasCoords ? { lat: lat, lng: lng } : STATION_FORM_MAP_DEFAULT;

            // El div pudo recrearse al re-renderizar el formulario: reiniciamos referencias.
            stationFormMarker = null;
            stationFormMap = new google.maps.Map(el, {
                center: center,
                zoom: 13,
                mapTypeId: 'hybrid',
                streetViewControl: false,
            });

            setStationFormMarker(center);

            stationFormMap.addListener('click', function (event) {
                const position = {
                    lat: event.latLng.lat(),
                    lng: event.latLng.lng(),
                };
                setStationFormMarker(position);
                syncStationFormLatLng(position.lat, position.lng);
            });

            return true;
        }

        function openStationFormMap(payload) {
            const data = Array.isArray(payload) ? payload[0] : payload;
            const lat = data ? parseFloat(data.lat) : NaN;
            const lng = data ? parseFloat(data.lon) : NaN;

            // El formulario acaba de renderizarse; reintentamos hasta que exista el div.
            let attempts = 0;
            (function tryInit() {
                if (initStationFormMap(lat, lng)) {
                    return;
                }
                if (attempts++ < 20) {
                    setTimeout(tryInit, 100);
                }
            })();
        }

        // Mantiene el marcador sincronizado si se edita Lat/Lon manualmente.
        document.addEventListener('change', function (event) {
            if (!stationFormMap || !event.target) {
                return;
            }
            if (event.target.id === 'latitudeAU' || event.target.id === 'longitudeAU') {
                const lat = parseFloat(document.getElementById('latitudeAU').value);
                const lng = parseFloat(document.getElementById('longitudeAU').value);
                if (!isNaN(lat) && !isNaN(lng)) {
                    const position = { lat: lat, lng: lng };
                    setStationFormMarker(position);
                    stationFormMap.panTo(position);
                }
            }
        });

        Livewire.on('openMapStationAU', openStationFormMap);
    })();
</script>

@stop


@section('footer')
    <div class="text-center">
    SenvaTec {{date('Y')}} | Desarrollado por <a href="https://www.artguz.com" style="color: #869099;">Artguz SRL</a> 
    </div>
@stop
