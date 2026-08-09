/* Logica del mapa de estaciones (Google Maps API + Livewire).
   Extraido de resources/views/station/map.blade.php.
   Depende de globals cargados aparte: google.maps, luxon, Swal, Livewire, window.stationMapTranslations. */


    /* Umbrales centralizados para distinguir retraso y caída de estaciones. */
    const STATION_STATUS_THRESHOLDS = Object.freeze({
        delayedStartHours: 3,
        offlineStartHours: 337,
    });

    // Variantes de color del marcador de alerta (rojo = activo/retraso, negro = offline).
    const STATION_ALERT_VARIANTS = Object.freeze({
        red: 'red',
        black: 'black',
    });

    // Tipos de icono de alerta: 'disabled' (estacion inhabilitada) y 'warning' (sin datos).
    const STATION_ALERT_ICONS = Object.freeze({
        disabled: 'disabled',
        warning: 'warning',
    });

    /* Configuración visual única para estados especiales de estación. */
    const STATION_STATUS_MARKER_CONFIG = Object.freeze({
        disabled: {
            color: '#FF0000',
            alertVariant: STATION_ALERT_VARIANTS.red,
            icon: STATION_ALERT_ICONS.disabled,
            internalOnly: true,
        },
        delayed: {
            color: '#DC2626',
            alertVariant: STATION_ALERT_VARIANTS.red,
            icon: STATION_ALERT_ICONS.warning,
            internalOnly: false,
        },
        offline: {
            color: '#111111',
            alertVariant: STATION_ALERT_VARIANTS.black,
            icon: STATION_ALERT_ICONS.warning,
            internalOnly: true,
        },
    });

    // Medidas y estilos comunes de los marcadores (tamano del punto y del icono de alerta).
    const STATION_MARKER_LAYOUT = Object.freeze({
        alertSizePx: '26px',
        dotSizePx: '14px',
        dotBorder: '2px solid white',
        dotTranslateY: 'translateY(7px)',
    });

    // Variables globales del mapa: instancia 'map', coordenadas y utilidades de marcadores.
    const labels = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";        
    let labelIndex = 0;       
    var array = new Array();
    var latitud = "";
    var longitud = "";       
    var coord;
    var myLatlng;
    var map;

    // Interacciones tras las que se conserva el encuadre actual (no recentra el mapa).
    const MAP_VIEW_INTERACTIONS_TO_PRESERVE = Object.freeze([
        'weather_filter',
        'refresh',
    ]);

    // Ultimo centro/zoom guardado para restaurar el encuadre entre renders del mapa.
    let persistedMapView = {
        center: null,
        zoom: null,
    };

    // Parametros del efecto de lluvia (zoom minimo, nro de gotas, velocidades, etc.).
    const RAIN_EFFECT_CONFIG = Object.freeze({
        minZoom: 14,
        requireRainTotal: true,
        minRainRate: 0,
        minRainTotal: 0,
        minDropsPerStation: 10,
        maxDropsPerStation: 90,
        rainRateDropMultiplier: 14,
        rainTotalDropBoostMultiplier: 1.5,
        maxRainRateForScale: 6,
        dropDriftX: -0.35,
        randomPointMaxAttempts: 32,
    });

    // Esta la estacion "retrasada"? (sin datos entre el umbral de retraso y el de caida).
    function isStationDelayed(hoursDifference) {
        return hoursDifference >= STATION_STATUS_THRESHOLDS.delayedStartHours
            && hoursDifference < STATION_STATUS_THRESHOLDS.offlineStartHours;
    }

    // Esta la estacion "caida"? (sin datos por encima del umbral offline).
    function isStationOffline(hoursDifference) {
        return hoursDifference >= STATION_STATUS_THRESHOLDS.offlineStartHours;
    }

    // Determina el estado especial de la estacion: 'disabled', 'offline', 'delayed' o null.
    function resolveStationStatusKey(station, hoursDifference, isInternalView) {
        if (isInternalView && station.stateStation == "0") {
            return 'disabled';
        }

        if (isStationOffline(hoursDifference)) {
            return isInternalView ? 'offline' : null;
        }

        if (isStationDelayed(hoursDifference)) {
            return 'delayed';
        }

        return null;
    }

    // Decide la apariencia del marcador: alerta (estado especial) o punto de color (clima).
    function resolveStationMarkerAppearance(station, hoursDifference, isInternalView, weatherType) {
        const statusKey = resolveStationStatusKey(station, hoursDifference, isInternalView);

        if (statusKey) {
            const statusConfig = STATION_STATUS_MARKER_CONFIG[statusKey];

            if (!statusConfig || (statusConfig.internalOnly && !isInternalView)) {
                return null;
            }

            return {
                kind: 'alert',
                statusKey,
                color: statusConfig.color,
                alertVariant: statusConfig.alertVariant,
                icon: statusConfig.icon,
            };
        }

        const weatherColor = resolveWeatherMarkerColor(station, weatherType);
        if (!weatherColor) {
            return null;
        }

        return {
            kind: 'dot',
            statusKey: null,
            color: weatherColor,
            alertVariant: null,
            icon: null,
        };
    }

    // Color del punto segun la variable mostrada (temperatura, viento o lluvia).
    function resolveWeatherMarkerColor(station, weatherType) {
        if (weatherType === 'temperature') {
            return calculateTemperatureColor(station.tempout);
        }

        if (weatherType === 'wind') {
            return calculateWindColor(station.windspeed);
        }

        if (weatherType === 'rain') {
            return calculateRainColor(station.raintotal);
        }

        return null;
    }

    // Devuelve el SVG del marcador de alerta (icono de inhabilitada o triangulo de warning).
    function buildAlertMarkerSvg({ color, variant, icon }) {
        if (icon === STATION_ALERT_ICONS.disabled) {
            return `
                <svg width="26" height="26" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="11" fill="${color}" stroke="white" stroke-width="2"/>
                    <circle cx="12" cy="12" r="7" fill="none" stroke="white" stroke-width="2"/>
                    <line x1="12" y1="5" x2="12" y2="3" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
            `;
        }

        const iconStroke = variant === STATION_ALERT_VARIANTS.black ? '#E5E7EB' : '#FFFFFF';

        return `
            <svg width="26" height="26" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <polygon points="12,2 2,22 22,22" fill="${color}" stroke="${iconStroke}" stroke-width="2"/>
                <circle cx="12" cy="15" r="1.5" fill="${iconStroke}"/>
                <line x1="12" y1="8" x2="12" y2="12" stroke="${iconStroke}" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
        `;
    }

    // Crea el DOM del punto de color de una estacion activa (con onda/pulso opcional).
    function buildDotMarkerContent(color, enablePulse = false) {
        const markerContent = document.createElement('div');
        markerContent.style.backgroundColor = color;
        markerContent.style.width = STATION_MARKER_LAYOUT.dotSizePx;
        markerContent.style.height = STATION_MARKER_LAYOUT.dotSizePx;
        markerContent.style.borderRadius = '50%';
        markerContent.style.border = STATION_MARKER_LAYOUT.dotBorder;
        markerContent.style.display = 'flex';
        markerContent.style.justifyContent = 'center';
        markerContent.style.alignItems = 'center';
        markerContent.style.transform = STATION_MARKER_LAYOUT.dotTranslateY;

        // Onda tipo radar opcional (CSS) para estaciones activas.
        if (enablePulse) {
            markerContent.classList.add('station-pulse-marker');
            markerContent.style.setProperty('--station-pulse-color', color);
        }

        return {
            content: markerContent,
            useCenteredAnchor: false,
        };
    }

    // Crea el DOM del marcador de alerta (SVG + clases de animacion CSS).
    function buildAlertMarkerContent(appearance) {
        const markerContent = document.createElement('div');
        markerContent.style.position = 'relative';
        markerContent.style.display = 'flex';
        markerContent.style.alignItems = 'center';
        markerContent.style.justifyContent = 'center';
        markerContent.style.width = STATION_MARKER_LAYOUT.alertSizePx;
        markerContent.style.height = STATION_MARKER_LAYOUT.alertSizePx;
        markerContent.innerHTML = buildAlertMarkerSvg({
            color: appearance.color,
            variant: appearance.alertVariant,
            icon: appearance.icon,
        });
        markerContent.classList.add('warning-alert-marker');

        if (appearance.alertVariant === STATION_ALERT_VARIANTS.black) {
            markerContent.classList.add('warning-alert-marker--black');
        }

        return {
            content: markerContent,
            useCenteredAnchor: true,
        };
    }

    // Construye el contenido del marcador: alerta o punto, segun la apariencia recibida.
    function buildStationMarkerContent(appearance, enablePulse = false) {
        if (appearance.kind === 'alert') {
            return buildAlertMarkerContent(appearance);
        }

        return buildDotMarkerContent(appearance.color, enablePulse);
    }

    // Devuelve el centro/zoom actual del mapa (o el ultimo persistido si aun no existe).
    function getCurrentMapView() {
        if (!map) {
            return {
                center: persistedMapView.center,
                zoom: persistedMapView.zoom,
            };
        }

        const currentCenter = map.getCenter();
        return {
            center: currentCenter ? {
                lat: currentCenter.lat(),
                lng: currentCenter.lng(),
            } : persistedMapView.center,
            zoom: map.getZoom() ?? persistedMapView.zoom,
        };
    }

    // Guarda el centro/zoom actuales para restaurarlos en el siguiente render del mapa.
    function persistCurrentMapView(targetMap = map) {
        if (!targetMap) {
            return;
        }

        const currentCenter = targetMap.getCenter();
        persistedMapView = {
            center: currentCenter ? {
                lat: currentCenter.lat(),
                lng: currentCenter.lng(),
            } : persistedMapView.center,
            zoom: targetMap.getZoom() ?? persistedMapView.zoom,
        };
    }

    // Funcion principal: crea el mapa de Google y pinta todas las estaciones recibidas.
    async function initMap(data = [], weather_type = 'temperature', goMarkerZoom = null, typeS = null, isInternalView = null, userVisibilityMapInternal = null, mapInteraction = 'refresh', enablePulseEffect = false, aviationStations = []) {
        console.log('AQUI EJECUTO!!!');
        const { Map } = await google.maps.importLibrary("maps");
        const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
        // Centro y zoom iniciales del mapa para este render.
        let coord, zoomLevel;
        // Vista actual usada para conservar el encuadre cuando corresponde.
        const currentMapView = getCurrentMapView();
        const shouldPreserveCurrentView = MAP_VIEW_INTERACTIONS_TO_PRESERVE.includes(mapInteraction)
            && currentMapView.center
            && currentMapView.zoom !== null;

        if (shouldPreserveCurrentView) {
            coord = currentMapView.center;
            zoomLevel = currentMapView.zoom;
        } else if (goMarkerZoom) {
            coord = { lat: parseFloat(goMarkerZoom.latitude), lng: parseFloat(goMarkerZoom.longitude) };
            zoomLevel = 12;
        } else {
            coord = { lat: parseFloat('-17.784697'), lng: parseFloat('-63.182028') };
            zoomLevel = 6.3;
        }

        map = new google.maps.Map(document.getElementById('map'), {
            center: coord,
            zoom: zoomLevel,
            mapId: "def05be188adae9d",
            mapTypeControl: true,
            mapTypeControlOptions: {
                mapTypeIds: ['roadmap', 'satellite'],
            },
        });

        // Actualizar indicador de zoom en tiempo real
        // Actualiza el indicador visual del nivel de zoom (texto, color y barra).
        function updateZoomIndicator() {
            const zoomLevelSpan = document.getElementById('zoom-level');
            const zoomStatus = document.getElementById('zoom-status');
            const zoomMeter = document.getElementById('zoom-meter');
            const zoomIndicator = document.querySelector('.map-zoom-indicator');
            if (zoomLevelSpan) {
                const currentZoom = map.getZoom();
                zoomLevelSpan.textContent = currentZoom.toFixed(1);

                let accentColor = '#3968a6';
                let statusText = 'Lejano';
                if (currentZoom >= 15) {
                    accentColor = '#27ae60';
                    statusText = 'Cercano';
                } else if (currentZoom >= 10) {
                    accentColor = '#f39c12';
                    statusText = 'Medio';
                }

                const zoomPercent = Math.max(0, Math.min(((currentZoom - 3) / 18) * 100, 100));

                if (zoomIndicator) {
                    zoomIndicator.style.setProperty('--zoom-accent', accentColor);
                }

                if (zoomStatus) {
                    zoomStatus.textContent = statusText;
                }

                if (zoomMeter) {
                    zoomMeter.style.width = `${zoomPercent}%`;
                }
            }
        }

        // Listener para actualizar zoom en tiempo real
        google.maps.event.addListener(map, 'zoom_changed', () => {
            updateZoomIndicator();
            persistCurrentMapView(map);
        });

        google.maps.event.addListener(map, 'idle', () => {
            persistCurrentMapView(map);
        });

        // Inicializar el indicador
        updateZoomIndicator();
        persistCurrentMapView(map);

        const { DateTime } = luxon;

        // ── Configuración de área de estación (antes en <x-station.area-overlay-script />) ──
        window.STATION_AREA_CONFIG = {
            useCircularShape: false,
            polygonSides: 8,
            radiusMeters: 3000,
            strokeWeight: 2,
            defaultFillOpacity: 0.2,
        };

        const STATION_AREA_CONFIG = window.STATION_AREA_CONFIG;

        // Calcula los vertices de un poligono regular (el "area" alrededor de la estacion).
        window.buildRegularPolygonPath = function buildRegularPolygonPath(center, radiusMeters, sides = STATION_AREA_CONFIG.polygonSides) {
            const safeSides = Math.max(3, sides);
            const earthRadius = 6378137;
            const centerLat = center.lat();
            const centerLng = center.lng();
            const centerLatRadians = centerLat * (Math.PI / 180);

            return Array.from({ length: safeSides }, (_, index) => {
                const angle = ((2 * Math.PI) / safeSides) * index - (Math.PI / 2);
                const latOffset = (radiusMeters * Math.sin(angle)) / earthRadius;
                const lngOffset = (radiusMeters * Math.cos(angle)) / (earthRadius * Math.cos(centerLatRadians));

                return {
                    lat: centerLat + (latOffset * 180) / Math.PI,
                    lng: centerLng + (lngOffset * 180) / Math.PI,
                };
            });
        };

        const buildRegularPolygonPath = window.buildRegularPolygonPath;

        // Crea el circulo o poligono del area de la estacion sobre el mapa.
        function createStationAreaOverlay({ center, color, fillOpacity }) {
            const baseOptions = {
                strokeColor: color,
                strokeOpacity: 1,
                strokeWeight: STATION_AREA_CONFIG.strokeWeight,
                fillColor: color,
                fillOpacity,
                map: map,
                editable: false,
                clickable: true,
            };

            if (STATION_AREA_CONFIG.useCircularShape) {
                return new google.maps.Circle({
                    ...baseOptions,
                    center,
                    radius: STATION_AREA_CONFIG.radiusMeters,
                });
            }

            return new google.maps.Polygon({
                ...baseOptions,
                paths: buildRegularPolygonPath(
                    center,
                    STATION_AREA_CONFIG.radiusMeters,
                    STATION_AREA_CONFIG.polygonSides
                ),
            });
        }

        (async () => {
            data.forEach((station) => {
                // Fechas en hora local para calcular retraso de datos por estacion.
                const fechaString = station.receipt_date;
                const fechaLaPaz = DateTime.fromFormat(fechaString, "yyyy-MM-dd HH:mm:ss", {
                    zone: "America/La_Paz"
                });
                const ahoraLaPaz = DateTime.now().setZone("America/La_Paz");
                const diferenciaHoras = ahoraLaPaz.diff(fechaLaPaz, "hours").hours;
                // Posicion y apariencia final del marcador segun estado y variable climatica.
                const markerPosition = new google.maps.LatLng(parseFloat(station.latitude), parseFloat(station.longitude));
                const markerAppearance = resolveStationMarkerAppearance(
                    station,
                    diferenciaHoras,
                    isInternalView,
                    weather_type
                );

                if (markerAppearance) {
                    const {
                        content: markerContent,
                        useCenteredAnchor,
                    } = buildStationMarkerContent(markerAppearance, enablePulseEffect);

                    const marker = new google.maps.marker.AdvancedMarkerElement({
                        position: markerPosition,
                        map: map,
                        content: markerContent,
                        title: station.name || stationMapTranslations.markers.title,
                        ...(useCenteredAnchor ? {
                            anchorLeft: '-50%',
                            anchorTop: '-50%',
                        } : {}),
                    });

                    // Determinar si mostrar relleno del círculo
                    const hasRain = parseFloat(station.raintotal) > 0.1;
                    const currentZoom = map.getZoom();
                    const shouldHideFill = hasRain && currentZoom >= 14;

                    const stationAreaOverlay = createStationAreaOverlay({
                        center: markerPosition,
                        color: markerAppearance.color,
                        fillOpacity: shouldHideFill ? 0 : STATION_AREA_CONFIG.defaultFillOpacity,
                    });

                    // Actualizar relleno del círculo cuando cambie el zoom
                    google.maps.event.addListener(map, 'zoom_changed', () => {
                        const newZoom = map.getZoom();
                        const shouldHideFillNow = hasRain && newZoom >= 14;
                        stationAreaOverlay.setOptions({
                            fillOpacity: shouldHideFillNow ? 0 : STATION_AREA_CONFIG.defaultFillOpacity
                        });
                    });

                    // Direcciones cardinales calculadas desde los grados de viento.
                    const direction1 = isValidNumber(station.winddir) ? getWindDirection(station.winddir) : '';
                    const direction2 = isValidNumber(station.winddir2) ? getWindDirection(station.winddir2) : '';
                    // QNH y formato de viento vienen del helper configurable de aviacion.
                    const aviationData = window.StationMapAviation
                        ? window.StationMapAviation.applyStationAviationData(station, aviationStations)
                        : { qnhText: null };
                    const result = aviationData.qnhText;
                    // HTML del popup con resumen meteorologico y acciones Livewire.
                    const infoWindowContent = window.StationMapInfoWindow.buildContent({
                        station,
                        direction1,
                        direction2,
                        qnhText: result,
                        translations: stationMapTranslations,
                    });

                    // Ventana informativa asociada al marcador y al area de la estacion.
                    const infoWindow = new google.maps.InfoWindow({
                        content: infoWindowContent
                    });

                    google.maps.event.addListener(marker, 'click', () => {
                        infoWindow.open(map, marker);
                    });

                    google.maps.event.addListener(stationAreaOverlay, 'click', function(event) {
                        infoWindow.open(map, marker);
                    });
                }
            });
        })();
    }

    // Valida que el valor recibido sea numerico y entero.
    function isValidNumber(value) {
        return value !== null && value !== undefined && !isNaN(value) && Number.isInteger(parseFloat(value));
    }

    // Convierte grados de viento a direccion cardinal abreviada.
    function getWindDirection(winddir) {
        if (winddir !== null && !isNaN(winddir)) {
            winddir = parseFloat(winddir);
            if (winddir >= 337.5 || winddir < 22.5) {
                return 'N';
            } else if (winddir >= 22.5 && winddir < 67.5) {
                return 'NE';
            } else if (winddir >= 67.5 && winddir < 112.5) {
                return 'E';
            } else if (winddir >= 112.5 && winddir < 157.5) {
                return 'SE';
            } else if (winddir >= 157.5 && winddir < 202.5) {
                return 'S';
            } else if (winddir >= 202.5 && winddir < 247.5) {
                return 'SO';
            } else if (winddir >= 247.5 && winddir < 292.5) {
                return 'O';
            } else if (winddir >= 292.5 && winddir < 337.5) {
                return 'NO';
            }
        }
        return '';
    }

    // Estado compartido del canvas y la animacion de lluvia.
    let rainCanvas = null;
    let rainCtx = null;
    let rainAreas = [];
    let rainAnimationId = null;
    let stationsData = [];

    // Obtiene la configuracion de area usada tambien por los overlays del mapa.
    function getStationAreaRainConfig() {
        return window.STATION_AREA_CONFIG || {
            useCircularShape: false,
            polygonSides: 8,
            radiusMeters: 3000,
        };
    }

    // Normaliza valores de lluvia invalidos a cero.
    function parseRainValue(value) {
        const parsedValue = parseFloat(value);
        return Number.isFinite(parsedValue) && parsedValue > 0 ? parsedValue : 0;
    }

    // Extrae intensidad y acumulado de lluvia de una estacion.
    function resolveStationRainMetrics(station) {
        return {
            rainRate: parseRainValue(station.rainrate),
            rainTotal: parseRainValue(station.raintotal),
        };
    }

    // Decide si una estacion debe mostrar gotas segun lluvia activa/acumulada.
    function shouldRenderStationRain(metrics) {
        const hasActiveRain = metrics.rainRate > RAIN_EFFECT_CONFIG.minRainRate;
        const hasAccumulatedRain = metrics.rainTotal > RAIN_EFFECT_CONFIG.minRainTotal;

        return RAIN_EFFECT_CONFIG.requireRainTotal
            ? hasActiveRain && hasAccumulatedRain
            : hasActiveRain;
    }

    // Calcula datos de proyeccion para convertir coordenadas del mapa al canvas.
    function getRainCanvasViewport() {
        const bounds = map.getBounds();
        const projection = map.getProjection();

        if (!bounds || !projection || !rainCanvas) {
            return null;
        }

        const ne = bounds.getNorthEast();
        const sw = bounds.getSouthWest();
        const scale = Math.pow(2, map.getZoom());
        const neWorldCoordinate = projection.fromLatLngToPoint(ne);
        const swWorldCoordinate = projection.fromLatLngToPoint(sw);

        return {
            projection,
            scale,
            neWorldCoordinate,
            swWorldCoordinate,
            worldWidth: (neWorldCoordinate.x - swWorldCoordinate.x) * scale,
            worldHeight: (swWorldCoordinate.y - neWorldCoordinate.y) * scale,
            canvasWidth: rainCanvas.width,
            canvasHeight: rainCanvas.height,
        };
    }

    // Convierte una LatLng de Google Maps a punto x/y dentro del canvas.
    function latLngToCanvasPoint(latLng, viewport) {
        const worldCoordinate = viewport.projection.fromLatLngToPoint(latLng);
        const pixelX = (worldCoordinate.x - viewport.swWorldCoordinate.x) * viewport.scale;
        const pixelY = (worldCoordinate.y - viewport.neWorldCoordinate.y) * viewport.scale;

        return {
            x: (pixelX / viewport.worldWidth) * viewport.canvasWidth,
            y: (pixelY / viewport.worldHeight) * viewport.canvasHeight,
        };
    }

    // Convierte metros reales a pixeles segun latitud y zoom actual.
    function metersToPixels(meters, latitude, zoom) {
        const metersPerPixel = 156543.03392 * Math.cos(latitude * Math.PI / 180) / Math.pow(2, zoom);
        return meters / metersPerPixel;
    }

    // Calcula los limites rectangulares de un poligono en coordenadas de canvas.
    function getPolygonBounds(points) {
        return points.reduce((bounds, point) => ({
            minX: Math.min(bounds.minX, point.x),
            maxX: Math.max(bounds.maxX, point.x),
            minY: Math.min(bounds.minY, point.y),
            maxY: Math.max(bounds.maxY, point.y),
        }), {
            minX: Infinity,
            maxX: -Infinity,
            minY: Infinity,
            maxY: -Infinity,
        });
    }

    // Construye la figura de lluvia de una estacion en el canvas (circulo o poligono).
    function buildStationRainShape(station, viewport, metrics) {
        const areaConfig = getStationAreaRainConfig();
        const latitude = parseFloat(station.latitude);
        const longitude = parseFloat(station.longitude);
        const centerLatLng = new google.maps.LatLng(latitude, longitude);
        const center = latLngToCanvasPoint(centerLatLng, viewport);
        const radiusPx = metersToPixels(areaConfig.radiusMeters, latitude, map.getZoom());

        if (areaConfig.useCircularShape) {
            return {
                type: 'circle',
                center,
                radius: radiusPx,
                bounds: {
                    minX: center.x - radiusPx,
                    maxX: center.x + radiusPx,
                    minY: center.y - radiusPx,
                    maxY: center.y + radiusPx,
                },
                metrics,
            };
        }

        const polygonPath = window.buildRegularPolygonPath(
            centerLatLng,
            areaConfig.radiusMeters,
            areaConfig.polygonSides
        );
        const points = polygonPath.map(point => latLngToCanvasPoint(
            new google.maps.LatLng(point.lat, point.lng),
            viewport
        ));

        return {
            type: 'polygon',
            center,
            points,
            bounds: getPolygonBounds(points),
            metrics,
        };
    }

    // Verifica si un punto esta dentro de un poligono usando ray casting.
    function isPointInPolygon(point, polygonPoints) {
        let isInside = false;

        for (let i = 0, j = polygonPoints.length - 1; i < polygonPoints.length; j = i++) {
            const current = polygonPoints[i];
            const previous = polygonPoints[j];
            const intersects = ((current.y > point.y) !== (previous.y > point.y))
                && (point.x < ((previous.x - current.x) * (point.y - current.y)) / (previous.y - current.y) + current.x);

            if (intersects) {
                isInside = !isInside;
            }
        }

        return isInside;
    }

    // Verifica si un punto cae dentro de la figura de lluvia.
    function isPointInsideRainShape(point, shape) {
        if (shape.type === 'circle') {
            const distanceFromCenter = Math.sqrt(
                Math.pow(point.x - shape.center.x, 2)
                + Math.pow(point.y - shape.center.y, 2)
            );

            return distanceFromCenter <= shape.radius;
        }

        return isPointInPolygon(point, shape.points);
    }

    // Genera una posicion aleatoria valida dentro del area de lluvia.
    function getRandomPointInRainShape(shape) {
        if (shape.type === 'circle') {
            const angle = Math.random() * Math.PI * 2;
            const distance = Math.sqrt(Math.random()) * shape.radius;

            return {
                x: shape.center.x + Math.cos(angle) * distance,
                y: shape.center.y + Math.sin(angle) * distance,
            };
        }

        for (let attempt = 0; attempt < RAIN_EFFECT_CONFIG.randomPointMaxAttempts; attempt++) {
            const point = {
                x: shape.bounds.minX + Math.random() * (shape.bounds.maxX - shape.bounds.minX),
                y: shape.bounds.minY + Math.random() * (shape.bounds.maxY - shape.bounds.minY),
            };

            if (isPointInsideRainShape(point, shape)) {
                return point;
            }
        }

        return shape.center;
    }

    // Calcula cuantas gotas mostrar segun intensidad y acumulado de lluvia.
    function calculateRainDropCount(metrics) {
        const rainRateIntensity = Math.min(metrics.rainRate, RAIN_EFFECT_CONFIG.maxRainRateForScale);
        const dropCount = RAIN_EFFECT_CONFIG.minDropsPerStation
            + Math.round(rainRateIntensity * RAIN_EFFECT_CONFIG.rainRateDropMultiplier)
            + Math.round(metrics.rainTotal * RAIN_EFFECT_CONFIG.rainTotalDropBoostMultiplier);

        return Math.max(
            RAIN_EFFECT_CONFIG.minDropsPerStation,
            Math.min(dropCount, RAIN_EFFECT_CONFIG.maxDropsPerStation)
        );
    }

    // Crea una gota con posicion, velocidad, largo y opacidad iniciales.
    function createRainDrop(shape, metrics) {
        const point = getRandomPointInRainShape(shape);
        const rainRateIntensity = Math.min(metrics.rainRate, RAIN_EFFECT_CONFIG.maxRainRateForScale);

        return {
            x: point.x,
            y: point.y,
            speed: 2 + Math.random() * 2.5 + rainRateIntensity * 0.25,
            length: 18 + Math.random() * 24 + rainRateIntensity * 2,
            opacity: Math.min(0.85, 0.38 + Math.random() * 0.25 + metrics.rainTotal * 0.015),
        };
    }

    // Reubica una gota al salir del area para mantener la lluvia continua.
    function resetRainDrop(drop, shape, metrics) {
        const point = getRandomPointInRainShape(shape);
        const rainRateIntensity = Math.min(metrics.rainRate, RAIN_EFFECT_CONFIG.maxRainRateForScale);

        drop.x = point.x;
        drop.y = point.y;
        drop.speed = 2 + Math.random() * 2.5 + rainRateIntensity * 0.25;
        drop.length = 18 + Math.random() * 24 + rainRateIntensity * 2;
        drop.opacity = Math.min(0.85, 0.38 + Math.random() * 0.25 + metrics.rainTotal * 0.015);
    }

    // Recorta el dibujo del canvas al area de lluvia de la estacion.
    function clipRainShape(shape) {
        rainCtx.beginPath();

        if (shape.type === 'circle') {
            rainCtx.arc(shape.center.x, shape.center.y, shape.radius, 0, Math.PI * 2);
        } else if (shape.points.length > 0) {
            rainCtx.moveTo(shape.points[0].x, shape.points[0].y);
            shape.points.slice(1).forEach(point => rainCtx.lineTo(point.x, point.y));
            rainCtx.closePath();
        }

        rainCtx.clip();
    }

    // Dibuja una gota individual con degradado y borde redondeado.
    function drawRainDrop(drop) {
        rainCtx.beginPath();
        const gradient = rainCtx.createLinearGradient(drop.x, drop.y, drop.x - 2, drop.y + drop.length);
        gradient.addColorStop(0, `rgba(200, 230, 255, ${drop.opacity})`);
        gradient.addColorStop(1, `rgba(100, 180, 255, ${drop.opacity * 0.5})`);

        rainCtx.strokeStyle = gradient;
        rainCtx.lineWidth = 2.5;
        rainCtx.lineCap = 'round';
        rainCtx.moveTo(drop.x, drop.y);
        rainCtx.lineTo(drop.x - 3, drop.y + drop.length);
        rainCtx.stroke();
    }

    // Función para crear efecto de lluvia dentro del área configurada de cada estación.
    function createRainEffect(stations, showEffect = true) {
        stationsData = stations;
        
        // Obtener o crear canvas
        rainCanvas = document.getElementById('rain-canvas');
        if (!rainCanvas) {
            console.error('Canvas de lluvia no encontrado en el DOM');
            return;
        }

        const mapContainer = document.getElementById('map');
        if (!mapContainer) {
            console.error('Contenedor del mapa no encontrado');
            return;
        }

        // Configurar canvas
        rainCanvas.width = mapContainer.offsetWidth;
        rainCanvas.height = mapContainer.offsetHeight;
        rainCanvas.style.display = showEffect ? 'block' : 'none';
        rainCtx = rainCanvas.getContext('2d');

        if (!showEffect) {
            stopRainEffect();
            return;
        }

        // Verificar nivel de zoom
        const currentZoom = map.getZoom();
        if (currentZoom < RAIN_EFFECT_CONFIG.minZoom) {
            stopRainEffect();
            return;
        }

        const viewport = getRainCanvasViewport();
        if (!viewport) {
            stopRainEffect();
            return;
        }

        rainAreas = stations.reduce((areas, station) => {
            const metrics = resolveStationRainMetrics(station);
            if (!shouldRenderStationRain(metrics)) {
                return areas;
            }

            const shape = buildStationRainShape(station, viewport, metrics);
            const dropCount = calculateRainDropCount(metrics);

            areas.push({
                shape,
                metrics,
                drops: Array.from({ length: dropCount }, () => createRainDrop(shape, metrics)),
            });

            return areas;
        }, []);

        if (rainAreas.length === 0) {
            stopRainEffect();
            return;
        }

        // Iniciar animación
        if (rainAnimationId) {
            cancelAnimationFrame(rainAnimationId);
        }
        animateRain();
    }

    // Bucle de animacion: dibuja, mueve y recicla gotas por estacion.
    function animateRain() {
        if (!rainCtx || !rainCanvas) return;

        rainCtx.clearRect(0, 0, rainCanvas.width, rainCanvas.height);

        rainAreas.forEach(area => {
            rainCtx.save();
            clipRainShape(area.shape);

            area.drops.forEach(drop => {
                drawRainDrop(drop);

                drop.y += drop.speed;
                drop.x += RAIN_EFFECT_CONFIG.dropDriftX;

                const dropTip = {
                    x: drop.x - 3,
                    y: drop.y + drop.length,
                };

                if (!isPointInsideRainShape(dropTip, area.shape) || drop.y > area.shape.bounds.maxY + 40) {
                    resetRainDrop(drop, area.shape, area.metrics);
                }
            });

            rainCtx.restore();
        });

        rainAnimationId = requestAnimationFrame(animateRain);
    }

    // Detiene la animacion y limpia la capa de lluvia.
    function stopRainEffect() {
        console.log('🛑 Deteniendo efecto de lluvia');
        if (rainAnimationId) {
            cancelAnimationFrame(rainAnimationId);
            rainAnimationId = null;
        }
        if (rainCanvas) {
            rainCanvas.style.display = 'none';
            if (rainCtx) {
                rainCtx.clearRect(0, 0, rainCanvas.width, rainCanvas.height);
            }
        }
        rainAreas = [];
    }

    // Actualizar canvas cuando el mapa se mueve o hace zoom
    function updateRainCanvas() {
        if (rainCanvas && map && stationsData.length > 0) {
            const mapContainer = document.getElementById('map');
            if (mapContainer) {
                const currentZoom = map.getZoom();
                
                rainCanvas.width = mapContainer.offsetWidth;
                rainCanvas.height = mapContainer.offsetHeight;
                
                // Solo mostrar lluvia desde el zoom configurado para cuidar rendimiento.
                if (currentZoom >= RAIN_EFFECT_CONFIG.minZoom) {
                    createRainEffect(stationsData, true);
                } else {
                    stopRainEffect();
                }
            }
        }
    }

    // Escucha Livewire para renderizar el mapa con estaciones actualizadas.
    Livewire.on('openMapStation', (event) => {
        const { data, weather_type, goMarkerZoom, typeS, isInternalView ,userVisibilityMapInternal, mapInteraction, enablePulseEffect, aviationStations } = event[0];

        setTimeout(() => {
            const mapContainer = document.getElementById('map');
            if (mapContainer && mapContainer.offsetHeight > 0) {
                initMap(data, weather_type, goMarkerZoom, typeS, isInternalView, userVisibilityMapInternal, mapInteraction, enablePulseEffect, aviationStations);
                
                // Agregar efecto de lluvia si el tipo es 'rain'
                if (weather_type === 'rain') {
                    setTimeout(() => {
                        createRainEffect(data, true);
                        
                        // Debounce para actualizar canvas cuando el mapa cambia
                        let resizeTimeout;
                        google.maps.event.addListener(map, 'zoom_changed', () => {
                            clearTimeout(resizeTimeout);
                            resizeTimeout = setTimeout(() => updateRainCanvas(), 500);
                        });
                        
                        google.maps.event.addListener(map, 'idle', () => {
                            if (rainCanvas && rainCanvas.style.display === 'block') {
                                updateRainCanvas();
                            }
                        });
                    }, 1500);
                } else {
                    stopRainEffect();
                }
            } else {
                console.error('No se encontró el contenedor del mapa.');
            }
        }, 700);
    });

    // Devuelve el color del marcador segun rango de temperatura.
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

    // Devuelve el color del marcador segun velocidad del viento.
    function calculateWindColor(windspeed) {
        if (windspeed <= 15) return '#EAEAD1EF';
        if (windspeed <= 34) return '#9C9080';
        if (windspeed <= 60) return '#857A53';
        return '#B28428B2';
    }

    // Devuelve el color del marcador segun lluvia acumulada.
    function calculateRainColor(raintotal) {
        // Promedios entre cortes de precipitacion para suavizar rangos.
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
