<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Tabla Personalizada en Pantalla Completa</title>

    {{-- Incluye los estilos de Livewire (esenciales) --}}
    @livewireStyles

    {{-- Font Awesome para iconos --}}
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    
    {{-- Tema Claro/Oscuro --}}
    <link rel="stylesheet" href="{{ asset('css/theme/themes.css') }}">
    <script>
        (function () {
            try {
                var s = localStorage.getItem('darkMode');
                var t = s === 'enabled' ? 'dark' : s === 'disabled' ? 'light' : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                document.documentElement.setAttribute('data-theme', t);
            } catch (e) {}
        })();
    </script>

    <style>        

        html, body {
        margin: 0;
        padding: 0;
        height: 100%;
        }

        table {
            width: 100vw;
            height: 100vh;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th, td {
            /*border: 1px solid #999;*/
            border-top: 1px solid #999;   /* Bordes horizontales superiores */
            border-bottom: 1px solid #999;/* Bordes horizontales inferiores */
            border-left: none;            /* Quitar bordes verticales izquierdos */
            border-right: none;           /* Quitar bordes verticales derechos */
            text-align: center;
            padding: 0px;
        }

        col:nth-child(1) { width: 7%; }
        col:nth-child(n+2):nth-child(-n+5) { width: 18.6%; }
        col:nth-child(6) { width: 18.6%; }
        /*
        tr:nth-child(1) {
            height: 8%;
            background-color:rgb(42, 162, 199);
        }

        tr:nth-child(2) {
            height: 14%;
            background-color:rgb(175, 199, 42);
        }

        tr:nth-child(3) {
            height: 22%;
            background-color:rgb(199, 42, 50);
        }

        tr:nth-child(4) {
            height: 40%;
            background-color:rgb(199, 42, 147);
        }

        tr:nth-child(5) {
            height: 8%;
            background-color:rgb(42, 199, 81); 
        }
        */        

        #map {
            width: 100%;
            height: 50%;
        }
        
        /* Estilos para la barra de controles flotante */
        .dark-mode-toggle:hover {
            background-color: rgba(0, 0, 0, 0.1) !important;
        }
        
        body[data-theme="dark"] .dark-mode-toggle:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
        }

        /* Pulso/onda tipo radar (CSS, liviano: solo transform/opacity en GPU). */
        .station-pulse-marker {
            position: relative;
            isolation: isolate;
        }
        .station-pulse-marker::before,
        .station-pulse-marker::after {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 2px solid var(--station-pulse-color, #3968a6);
            transform: translate(-50%, -50%) scale(1);
            opacity: 0;
            pointer-events: none;
            z-index: -1;
            will-change: transform, opacity;
            animation: station-pulse-wave 2.4s ease-out infinite;
        }
        .station-pulse-marker::after {
            animation-delay: 1.2s;
        }
        @keyframes station-pulse-wave {
            0% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 0.55;
            }
            70% {
                opacity: 0.15;
            }
            100% {
                transform: translate(-50%, -50%) scale(3.6);
                opacity: 0;
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .station-pulse-marker::before,
            .station-pulse-marker::after {
                animation: none;
            }
        }

    </style>
</head>
<body style="background-color: rgb(255, 255, 255)">
    {{-- Barra superior con controles de idioma y tema --}}
    <div style="position: fixed; top: 10px; right: 10px; z-index: 9999; display: flex; align-items: center; gap: 10px; background-color: rgba(255, 255, 255, 0.95); padding: 8px 12px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
        {{-- Selector de idiomas --}}
        <livewire:components.language-select triggerClass="hover:bg-slate-200"/>
        
        {{-- Toggle de modo oscuro --}}
        <button class="dark-mode-toggle" onclick="toggleDarkMode()" title="Modo Oscuro/Claro" style="background: none; border: none; cursor: pointer; padding: 8px; border-radius: 4px; transition: background-color 0.3s;">
            <i class="fas fa-moon" style="font-size: 1.2rem; color: #333;"></i>
        </button>
    </div>
    
    @livewire('fundecor-dashboard.fundecor-dashboard', [ 'boardId' => session('boardId')])

    {{-- Incluye los scripts de Livewire (esenciales) --}}
    @livewireScripts

    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}" async defer></script>
    @vite(['resources/js/app.js'])

    <script>
       //document.addEventListener('DOMContentLoaded', function () {
            /*
            const toggleFullscreenBtn = document.getElementById('toggleFullscreenBtn');
            const fullscreenText = toggleFullscreenBtn.querySelector('.fullscreen-text');
            const exitFullscreenText = toggleFullscreenBtn.querySelector('.exit-fullscreen-text');
            const fullscreenElement = document.getElementById('contenedorDeMiTabla');

            toggleFullscreenBtn.addEventListener('click', function() {
                if (!document.fullscreenElement) {
                    // Si no estamos en modo fullscreen, lo solicitamos para el elemento
                    if (fullscreenElement.requestFullscreen) {
                        fullscreenElement.requestFullscreen();
                    } else if (fullscreenElement.mozRequestFullScreen) { // Firefox 
                        fullscreenElement.mozRequestFullScreen();
                    } else if (fullscreenElement.webkitRequestFullscreen) { // Chrome, Safari & Opera 
                        fullscreenElement.webkitRequestFullscreen();
                    } else if (fullscreenElement.msRequestFullscreen) { // IE/Edge 
                        fullscreenElement.msRequestFullscreen();
                    }
                } else {
                    // Si estamos en modo fullscreen, salimos
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    } else if (document.mozCancelFullScreen) { // Firefox 
                        document.mozCancelFullScreen();
                    } else if (document.webkitExitFullscreen) { // Chrome, Safari & Opera 
                        document.webkitExitFullscreen();
                    } else if (document.msExitFullscreen) { // IE/Edge 
                        document.msExitFullscreen();
                    }
                }
            });

            // Opcional: Cambiar el texto del botón cuando se entra/sale de pantalla completa
            document.addEventListener('fullscreenchange', updateButtonText);
            document.addEventListener('mozfullscreenchange', updateButtonText);
            document.addEventListener('webkitfullscreenchange', updateButtonText);
            document.addEventListener('msfullscreenchange', updateButtonText);

            function updateButtonText() {
                if (document.fullscreenElement) {
                    fullscreenText.style.display = 'none';
                    exitFullscreenText.style.display = 'inline';
                } else {
                    fullscreenText.style.display = 'inline';
                    exitFullscreenText.style.display = 'none';
                }
            }

            */


            // Construye el dot del marcador con onda CSS opcional.
            function buildDashboardDotMarker(color, withPulse) {
                const markerContent = document.createElement('div');
                markerContent.style.backgroundColor = color;
                markerContent.style.width = '14px';
                markerContent.style.height = '14px';
                markerContent.style.borderRadius = '50%';
                markerContent.style.border = '2px solid white';
                markerContent.style.display = 'flex';
                markerContent.style.justifyContent = 'center';
                markerContent.style.alignItems = 'center';
                markerContent.style.transform = 'translateY(7px)';

                if (withPulse) {
                    markerContent.classList.add('station-pulse-marker');
                    markerContent.style.setProperty('--station-pulse-color', color);
                }

                return markerContent;
            }

            async function initMap(data = [], weather_type = 'temperature', enablePulseEffect = false) {
                console.log('AQUI EJECUTO!!!');
                const { Map } = await google.maps.importLibrary("maps");
                const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
                let coord, zoomLevel;
                coord = { lat: parseFloat('-21.719355'), lng: parseFloat('-64.675580') };
                zoomLevel = 11.5;


                map = new google.maps.Map(document.getElementById('map'), {
                    center: coord,
                    zoom: zoomLevel,
                    heading: 45,  
                    mapId: "def05be188adae9d",
                    mapTypeId: 'roadmap', // o 'satellite',
                    mapTypeControl: false 
                });
                
                const colorStripWrapper = document.createElement('div');
                colorStripWrapper.style.position = 'absolute';
                
                colorStripWrapper.style.left = '15px';
                colorStripWrapper.style.top = '15px';
                colorStripWrapper.style.zIndex = '999';
                colorStripWrapper.style.pointerEvents = 'none';

                //Contenedor visual
                const colorStripContainer = document.createElement('div');
                colorStripContainer.style.position = 'relative';
                colorStripContainer.style.width = '75px';
                colorStripContainer.style.height = '30vh'; // altura relativa al viewport
                colorStripContainer.style.backgroundColor = '#7E7E7EFF';// 'rgba(0, 0, 0, 0.8)';
                
                colorStripContainer.style.border = '1px solid #444';
                colorStripContainer.style.borderRadius = '6px';
                colorStripContainer.style.padding = '6px';               
                colorStripContainer.style.boxSizing = 'border-box';
                colorStripContainer.style.display = 'flex';
                colorStripContainer.style.flexDirection = 'column';
                colorStripContainer.style.justifyContent = 'center';

                //Franja de degradado
                const colorStrip = document.createElement('div');
                colorStrip.style.position = 'absolute';
                colorStrip.style.left = '6px'; // dentro del padding
                colorStrip.style.top = '6px';
                colorStrip.style.width = '20px';
                colorStrip.style.height = `calc(100% - 12px)`; // resta el padding
                colorStrip.style.background = 'linear-gradient(to bottom, red, orange, yellow, green, blue)';
                colorStrip.style.opacity = '0.85';
                colorStrip.style.border = '1px solid #444';
                colorStrip.style.borderRadius = '4px';

                //Etiquetas de temperatura
                const temperatures = ['40°C', '35°C', '25°C', '15°C', '5°C', '0°C', '-5°C', '-10°C', '-15°C'];

                temperatures.forEach((temp, index) => {
                    const label = document.createElement('div');
                    label.textContent = temp;
                    label.style.position = 'absolute';
                    label.style.left = '32px'; // a la derecha de la franja
                    label.style.top = `calc(${(index / (temperatures.length - 1)) * 100}% + 16px)`; // ajustado por padding
                    label.style.transform = 'translateY(-50%)';
                    label.style.fontSize = '0.65rem';
                    label.style.color = '#fff';
                    label.style.textShadow = '0 0 2px black';
                    label.style.pointerEvents = 'none';
                    if (index === 0) {
                        //label.style.marginTop = '9px'; // puedes ajustar el valor
                    }
                    colorStripContainer.appendChild(label);
                });

                // Agrega la franja al contenedor
                colorStripContainer.appendChild(colorStrip);
                colorStripWrapper.appendChild(colorStripContainer);

                // Inserta todo en el mapa
                document.getElementById('map').appendChild(colorStripWrapper);


                const { DateTime } = luxon;

                (async () => {
                    data.forEach((station) => {
                        const fechaString = station.receipt_date;
                        const fechaLaPaz = DateTime.fromFormat(fechaString, "yyyy-MM-dd HH:mm:ss", {
                            zone: "America/La_Paz"
                        });
                        const ahoraLaPaz = DateTime.now().setZone("America/La_Paz");
                        const diferenciaHoras = ahoraLaPaz.diff(fechaLaPaz, "hours").hours;
                        let color;

                        const markerPosition = new google.maps.LatLng(parseFloat(station.latitude), parseFloat(station.longitude));
                        
                        if (diferenciaHoras >= 3 ) {
                            color = '#999999';                               
                        } else {
                            if (weather_type === 'temperature') {
                                color = calculateTemperatureColor(station.tempout);
                            } else if (weather_type === 'wind') {
                                color = calculateWindColor(station.windspeed);
                            } else if (weather_type === 'rain') {
                                color = calculateRainColor(station.raintotal);
                            }
                        } 

                        if (color) {
                            // Las no funcionales (sin datos recientes) conservan su pulso
                            // siempre; las activas solo si la bandera lo habilita.
                            const isOffline = diferenciaHoras >= 3;
                            const markerContent = buildDashboardDotMarker(color, enablePulseEffect || isOffline);

                            const marker = new google.maps.marker.AdvancedMarkerElement({
                                position: markerPosition,
                                map: map,
                                content: markerContent,
                            });
                        }
                    });
                })();
            }

            function calculateTemperatureColor(tempout) {
                if (tempout <= -15) return '#2030B0';
                if (tempout <= -10) return '#205FB0';
                if (tempout <= -5) return '#258DB9';
                if (tempout <= 4) return '#28B238';
                if (tempout <= 14) return '#EBDC1B';
                if (tempout <= 24) return '#EAB241';
                if (tempout <= 34) return '#E88C13';
                if (tempout <= 39) return '#B61B34';
                return '#3D1396';
            }

            function calculateWindColor(windspeed) {
                if (windspeed <= 15) return '#EAEAD1EF';
                if (windspeed <= 34) return '#9C9080';
                if (windspeed <= 60) return '#857A53';
                return '#B28428B2';
            }

            function calculateRainColor(raintotal) {
                let averages = [];
                let precipitationParts = [18, 16, 14, 11, 9, 7, 5, 2, 0.1, 0];
                for (let i = 0; i < precipitationParts.length - 1; i++) {
                    let average = (precipitationParts[i] + precipitationParts[i + 1]) / 2.0;
                    averages.push(average);
                }

                if (raintotal >= averages[0]) {
                    return '#50245e';
                } else if (raintotal >= averages[1] && raintotal < averages[0]) {
                return '#a61414';                
                } else if (raintotal >= averages[2] && raintotal < averages[1]) {
                return '#dc5c0c';
                } else if (raintotal >= averages[3] && raintotal < averages[2]) {
                return '#ea9155';
                } else if (raintotal >= averages[4] && raintotal < averages[3]) {
                return '#e2c726';
                } else if (raintotal >= averages[5] && raintotal < averages[4]) {
                return '#199d60';
                } else if (raintotal >= averages[6] && raintotal < averages[5]) {
                return '#1D5C63';
                } else if (raintotal >= averages[7] && raintotal < averages[6]) {
                return '#55B6DCFF';
                } else if (raintotal < averages[7]) {
                if (raintotal == 0.0) {
                    return '#FFFFFF';
                } else {
                    return '#2056CAFF';
                }
                }else{
                return '#2E359FB2';
                }
                
            }

            Livewire.on('getMapStation', (event) => {
                const { data, weather_type, typeS, enablePulseEffect } = event[0];
                setTimeout(() => {
                    const mapContainer = document.getElementById('map');
                    if (mapContainer && mapContainer.offsetHeight > 0) {
                        initMap(data, weather_type, enablePulseEffect);
                    } else {
                        console.error('No se encontró el contenedor del mapa.');
                    }
                }, 1700);
            });
       // });
    </script>
    
    <script src="{{ asset('js/dark-mode.js') }}"></script>
</body>
</html>


 
