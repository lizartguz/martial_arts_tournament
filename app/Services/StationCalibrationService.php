<?php

namespace App\Services;

use App\Models\StationCalibrationM;
use App\Utils\Utils;

class StationCalibrationService
{
    protected array $methodMap = [
        'wind_direction_offset_180_v1' => 'applyWindDirectionOffset180',
        'wind_speed_ms_to_kmh_v1' => 'applyWindSpeedMsToKmh',
    ];

    public function applyToPayload(int $stationId, array $payload): array
    {
        if (empty($payload)) {
            return $payload;
        }

        $calibrations = StationCalibrationM::query()
            ->active()
            ->where('station_id', $stationId)
            ->whereIn('variable_name', array_keys($payload))
            ->get()
            ->keyBy('variable_name');

        foreach ($calibrations as $variableName => $calibration) {
            if (!array_key_exists($variableName, $payload)) {
                continue;
            }

            $payload[$variableName] = $this->applyCalibration(
                $calibration->calibration_key,
                $payload[$variableName],
                (array) ($calibration->calibration_params ?? [])
            );
        }

        return $this->syncDerivedWindFields($payload);
    }

    protected function applyCalibration(string $calibrationKey, $value, array $params = [])
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $method = $this->methodMap[$calibrationKey] ?? null;

        if (!$method || !method_exists($this, $method)) {
            return $value;
        }

        return $this->{$method}($value, $params);
    }

    // Corrige la direccion del viento aplicando un desfase angular y normalizando a 0-359.
    protected function applyWindDirectionOffset180($value, array $params = [])
    {
        $numericValue = $this->toFloatOrNull($value);

        if ($numericValue === null) {
            return $value;
        }

        $offset = isset($params['offset']) ? (float) $params['offset'] : 180.0;
        $normalized = fmod($numericValue + $offset, 360);

        if ($normalized < 0) {
            $normalized += 360;
        }

        return round($normalized, 2);
    }

    // Convierte velocidades de viento recibidas en m/s al formato base del sistema en km/h.
    protected function applyWindSpeedMsToKmh($value, array $params = [])
    {
        $numericValue = $this->toFloatOrNull($value);

        if ($numericValue === null) {
            return $value;
        }

        $factor = isset($params['factor']) ? (float) $params['factor'] : 3.6;

        return round($numericValue * $factor, 2);
    }

    // Recalcula los textos cardinales para que coincidan con los grados ya calibrados.
    protected function syncDerivedWindFields(array $payload): array
    {
        if (array_key_exists('winddir', $payload)) {
            $payload['winddirstr'] = Utils::windDirToStr(
                $payload['winddir'],
                $payload['windspeed'] ?? 0
            );
        }

        if (array_key_exists('winddir2', $payload)) {
            $payload['winddirstr2'] = Utils::windDirToStr(
                $payload['winddir2'],
                $payload['windspeed2'] ?? 0
            );
        }

        return $payload;
    }

    protected function toFloatOrNull($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }
}
