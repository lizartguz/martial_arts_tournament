<?php

use App\Http\Controllers\CampbellC;
use App\Http\Controllers\DailyTotalC;
use App\Http\Controllers\NotificationAlertAlarmC;
use App\Jobs\CollectStationForecast;
use App\Models\IpRegistration;
use App\Models\StationM;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

Artisan::command('app:log-ip-command', function () {
    try {
        DB::beginTransaction();
        IpRegistration::truncate();
        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        $this->error('Error al registrar la IP: ' . $e->getMessage());
        Log::error('Error al registrar IP: ' . $e->getMessage());
    }
})->dailyAt('00:02')->timezone('America/La_Paz');

Artisan::command('app:daily-data', function () {
    Log::info('----- INICIO app:daily-data-command -----');
    $c = new DailyTotalC();
    $request = new Request();
    $c->setDailyTotal($request);
    Log::info('----- FIN app:daily-data-command -----');
})->cron('59 * * * *')->timezone('America/La_Paz');

Artisan::command('app:campbell-command', function () {
    $c = new CampbellC();
    $c->run();
})->cron('*/11 * * * *')->timezone('America/La_Paz');

// Artisan::command('app:verif-pay', function () {
//     $p = new VerifPayC();
//     $p->verif();
// })->dailyAt('00:00')->timezone('America/La_Paz');

Artisan::command('app:notification-alarm', function () {
    NotificationAlertAlarmC::runAlarm();
})->cron('*/11 * * * *')->timezone('America/La_Paz');

Artisan::command('app:notification-alert', function () {
    NotificationAlertAlarmC::runAlert();
})->dailyAt('11:00')->timezone('America/La_Paz');

Artisan::command('app:notification-dispatch-push', function (PushNotificationService $pushNotificationService) {
    $result = $pushNotificationService->dispatchPendingNotifications();

    Log::info('Resumen de envio de avisos push.', $result);
    $this->info('Avisos procesados: ' . ($result['processed'] ?? 0));
})->everyFiveMinutes()->timezone('America/La_Paz');

//----------------------------- FORECAST API GET ---------------------------------
Artisan::command('app:forecast-api-800', function () {
    try {
        $stationIds = StationM::query()
            ->orderBy('id')
            ->pluck('id');

        foreach ($stationIds as $stationId) {
            CollectStationForecast::dispatch((int) $stationId);
        }

        Log::info('Trabajos de pronostico publicados en RabbitMQ.', [
            'stations' => $stationIds->count(),
            'queue' => 'forecasts',
        ]);

        $this->info("Pronosticos encolados: {$stationIds->count()}");
    } catch (\Throwable $e) {
        Log::error('Error publicando pronosticos en RabbitMQ.', [
            'error' => $e->getMessage(),
        ]);

        throw $e;
    }
})->dailyAt('08:15')
    ->timezone('America/La_Paz')
    ->withoutOverlapping(60);

//----------------------------- PREPARAR DB SIGUIENTE ANO ---------------------------------
// Crea automaticamente la DB y tabla senva_data del siguiente ano.
// Se activa 30 dias antes de fin de ano.
Artisan::command('app:prepare-next-year-db', function () {
    $this->call(\App\Console\Commands\PrepareNextYearDbCommand::class);
})->dailyAt('00:05')->timezone('America/La_Paz');
