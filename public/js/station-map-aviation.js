/* Utilidades de aviacion para el mapa de estaciones.
   Mantiene QNH y formato de viento fuera del render principal del mapa. */

(function registerStationMapAviation(window) {
    const AVIATION_DEFAULTS = Object.freeze({
        qnhPressureCorrection: 0.3,
        standardPressureMb: 1013.25,
        temperatureKelvin: 288,
        temperatureLapseRate: 0.0065,
        pressureExponent: 0.190284,
        qnhExponent: 5.2553026,
        mbToInHg: 33.8639,
        kmhPerKnot: 1.852,
    });

    const WIND_FIELDS = Object.freeze([
        'windspeed',
        'windspeed2',
        'windspeedhi',
        'windspeedhi2',
    ]);

    // Normaliza la lista recibida desde Livewire para buscar por stationId.
    function normalizeAviationStations(aviationStations = []) {
        if (Array.isArray(aviationStations)) {
            return aviationStations;
        }

        return Object.entries(aviationStations).map(([stationId, config]) => ({
            stationId: Number(stationId),
            ...config,
        }));
    }

    // Devuelve la configuracion de aviacion de una estacion, si existe.
    function findAviationStationConfig(station, aviationStations = []) {
        const stationId = Number(station.stationId ?? station.id);

        return normalizeAviationStations(aviationStations).find((config) => (
            Number(config.stationId) === stationId
        )) || null;
    }

    // Indica si una medicion viene vacia o con el sentinel historico -9999.
    function isMissingMeasurement(value) {
        if (value === null || value === undefined || value === '') {
            return true;
        }

        const numericValue = parseFloat(value);
        return !Number.isFinite(numericValue) || numericValue === -9999;
    }

    // Formatea un campo de viento en km/h o nudos, segun la estacion.
    function formatWindValue(value, useKnots = false) {
        const unit = useKnots ? 'Kt' : 'km/h';

        if (isMissingMeasurement(value)) {
            return `NA ${unit}`;
        }

        const numericValue = parseFloat(value);
        const convertedValue = useKnots
            ? numericValue / AVIATION_DEFAULTS.kmhPerKnot
            : numericValue;

        return `${Math.round(convertedValue)} ${unit}`;
    }

    // Aplica formato a todos los campos de viento visibles en el popup.
    function formatStationWindFields(station, aviationConfig = null) {
        const knotFields = new Set(aviationConfig?.windFieldsInKnots || []);

        WIND_FIELDS.forEach((field) => {
            station[field] = formatWindValue(station[field], knotFields.has(field));
        });
    }

    // Calcula QNH e inHg para estaciones con altura configurada.
    function calculateQnhText(station, aviationConfig = null) {
        if (!aviationConfig || isMissingMeasurement(station.press)) {
            return null;
        }

        const altitudeMeters = Number(aviationConfig.altitudeMeters);
        const pressureMb = parseFloat(station.press);

        if (!Number.isFinite(altitudeMeters) || !Number.isFinite(pressureMb)) {
            return null;
        }

        const adjustedPressure = pressureMb - AVIATION_DEFAULTS.qnhPressureCorrection;
        if (adjustedPressure <= 0) {
            return null;
        }

        const pressureRatio = AVIATION_DEFAULTS.standardPressureMb / adjustedPressure;
        const pressurePower = Math.pow(pressureRatio, AVIATION_DEFAULTS.pressureExponent);
        const altitudeFactor = (
            pressurePower
            * AVIATION_DEFAULTS.temperatureLapseRate
            * altitudeMeters
            / AVIATION_DEFAULTS.temperatureKelvin
        );
        const qnhMb = adjustedPressure * Math.pow(1 + altitudeFactor, AVIATION_DEFAULTS.qnhExponent);
        const qnhInHg = qnhMb / AVIATION_DEFAULTS.mbToInHg;

        return `Q${Math.round(qnhMb)}mb   A${qnhInHg.toFixed(2)}inHG`;
    }

    // Punto de entrada usado por station-map.js antes de construir el popup.
    function applyStationAviationData(station, aviationStations = []) {
        const aviationConfig = findAviationStationConfig(station, aviationStations);

        formatStationWindFields(station, aviationConfig);

        return {
            qnhText: calculateQnhText(station, aviationConfig),
            config: aviationConfig,
        };
    }

    window.StationMapAviation = Object.freeze({
        applyStationAviationData,
        calculateQnhText,
        findAviationStationConfig,
        formatStationWindFields,
    });
})(window);
