@props([
    'useCircularShape' => false,
    'polygonSides' => 10,
    'radiusMeters' => 3000,
    'strokeWeight' => 2,
    'defaultFillOpacity' => 0.2,
])

window.STATION_AREA_CONFIG = {
    useCircularShape: @js((bool) $useCircularShape),
    polygonSides: @js((int) $polygonSides),
    radiusMeters: @js((int) $radiusMeters),
    strokeWeight: @js((int) $strokeWeight),
    defaultFillOpacity: @js((float) $defaultFillOpacity),
};

const STATION_AREA_CONFIG = window.STATION_AREA_CONFIG;

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
