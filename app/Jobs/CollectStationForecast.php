<?php

namespace App\Jobs;

use App\Http\Controllers\ForecastC;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class CollectStationForecast implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 600;
    public int $uniqueFor = 1800;
    public bool $failOnTimeout = true;

    public function __construct(public int $stationId)
    {
        $this->onConnection('rabbitmq');
        $this->onQueue('forecasts');
    }

    /**
     * Recolecta y persiste el pronostico de una sola estacion.
     */
    public function handle(ForecastC $forecastController): void
    {
        Log::info('Iniciando recoleccion de pronostico en RabbitMQ.', [
            'station_id' => $this->stationId,
        ]);

        $request = new Request([
            'station_id' => $this->stationId,
        ]);

        $result = $forecastController->getForecastCurl($request, true);

        if (!empty($result['errors'])) {
            throw new RuntimeException(json_encode($result['errors'], JSON_UNESCAPED_UNICODE));
        }

        Log::info('Recoleccion de pronostico completada.', [
            'station_id' => $this->stationId,
        ]);
    }

    /**
     * Espera progresivamente antes de reintentar una API temporalmente fallida.
     */
    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function uniqueId(): string
    {
        return (string) $this->stationId;
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Fallo definitivo recolectando el pronostico de una estacion.', [
            'station_id' => $this->stationId,
            'error' => $exception?->getMessage(),
        ]);
    }
}
