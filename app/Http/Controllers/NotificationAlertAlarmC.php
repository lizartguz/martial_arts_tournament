<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\AlarmStoreM;
use App\Models\AlertAlarmM;
use App\Models\AlertStoreM;
use App\Services\PushNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationAlertAlarmC extends Controller
{
    public static function runAlarm (){
        date_default_timezone_set('America/La_Paz');
        DB::table('alarm_store')->truncate();
        AlertAlarmM::leftJoin('stations as s', 'alerts_alarms.station_id', '=', 's.id')
        ->leftJoin('current_data as cd', 'cd.station_id', '=', 's.id')
        ->where('alerts_alarms.notification_status', 1)
        ->where('alerts_alarms.state', 1)
        ->where('alerts_alarms.type', "alarm")
        ->where('s.state', 1)
        ->select(
            'alerts_alarms.temp_max',
            'alerts_alarms.temp_min',
            'alerts_alarms.dewpoint',
            'alerts_alarms.wind_max',
            'alerts_alarms.rain_total',
            'alerts_alarms.uv_max',
            'alerts_alarms.solar_rad_max',
            'alerts_alarms.dt_max',
            'alerts_alarms.dt_min',
            'alerts_alarms.type',
            'cd.id',
            'cd.receipt_date',
            'cd.tempout',
            'cd.humout',
            'cd.windspeed',
            'cd.windspeed2',
            'cd.windspeedhi',
            'cd.raintotal',
            'cd.uvindex',
            'cd.solrad',
            'cd.dewptout',
            'cd.press',
            'alerts_alarms.station_id',
            'alerts_alarms.user_id'
        )
        ->orderBy('alerts_alarms.user_id')
        ->orderBy('alerts_alarms.station_id')
        ->chunk(100, function ($registros) {   
            $listAlarm = [];
            foreach ($registros as $registro) {
                try {
                    
                    if($registro->type=="alarm"){                        
                       
                        if(!is_null($registro->tempout) && !is_null($registro->temp_max) && round($registro->temp_max) >= round($registro->tempout) ){
                            $listAlarm[] = [
                                'station_id' => $registro->station_id,
                                'user_id' => $registro->user_id,
                                'reg_date' => $registro->receipt_date,
                                'value' => $registro->tempout,
                                'variable' => "temperatura",
                                'unit' => "°C",
                                'state' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }

                        if(!is_null($registro->tempout) && !is_null($registro->temp_min) && round($registro->temp_min) <= round($registro->tempout)){
                            $listAlarm[] = [
                                'station_id' => $registro->station_id,
                                'user_id' => $registro->user_id,
                                'reg_date' => $registro->receipt_date,
                                'value' => $registro->tempout,
                                'variable' => "temperatura",
                                'unit' => "°C",
                                'state' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }

                        if(!is_null($registro->tempout) && !is_null($registro->humout) ){
                            if (round($registro->humout) >= round($registro->tempout) - 2) { //"⚠️ Punto de Rocío Alto" (formula excel);                                
                                $listAlarm[] = [
                                    'station_id' => $registro->station_id,
                                    'user_id' => $registro->user_id,
                                    'reg_date' => $registro->receipt_date,
                                    'value' => $registro->dewpoint,
                                    'variable' => "punto_rocio",
                                    'unit' => "°C",
                                    'state' => 1,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                        }
                        

                        if(!is_null($registro->wind_max) && !is_null($registro->windspeedhi) && round($registro->wind_max) >= round($registro->windspeedhi) ){
                            $listAlarm[] = [
                                'station_id' => $registro->station_id,
                                'user_id' => $registro->user_id,
                                'reg_date' => $registro->receipt_date,
                                'value' => $registro->windspeedhi,
                                'variable' => "viento",
                                'unit' => "km/h",
                                'state' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }

                        if(!is_null($registro->rain_total) && !is_null($registro->raintotal) && round($registro->rain_total) >= round($registro->raintotal) ){
                            $listAlarm[] = [
                                'station_id' => $registro->station_id,
                                'user_id' => $registro->user_id,
                                'reg_date' => $registro->receipt_date,
                                'value' => $registro->raintotal,
                                'variable' => "lluvia",
                                'unit' => "mm",
                                'state' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }

                        if(!is_null($registro->solar_rad_max) && !is_null($registro->solrad) && round($registro->solrad) >= round($registro->solar_rad_max) ){
                            $listAlarm[] = [
                                'station_id' => $registro->station_id,
                                'user_id' => $registro->user_id,
                                'reg_date' => $registro->receipt_date,
                                'value' => $registro->solrad,
                                'variable' => "radiación solar",
                                'unit' => "W/m²",
                                'state' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }

                        if(!is_null($registro->uv_max) && !is_null($registro->uvindex) && round($registro->uv_max) >= round($registro->uvindex) ){
                            $listAlarm[] = [
                                'station_id' => $registro->station_id,
                                'user_id' => $registro->user_id,
                                'reg_date' => $registro->receipt_date,
                                'value' => $registro->uvindex,
                                'variable' => "uv",
                                'unit' => "",
                                'state' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }

                        if($registro->dewptout!=null && $registro->tempout!=null && $registro->press!=null){
                            $detalT = null;
                            try {
                                $value = (6.11 * pow(10,(7.5 *$registro->dewptout / (237.7 + $registro->dewptout))));
                                $bulboHumedoMin = (((0.00066 * $registro->press) *  $registro->tempout) + ((4098 * $value) / pow(($registro->dewptout + 237.7) , 2) * $registro->dewptout)) / ((0.00066 *  $registro->press ) + (4098 * $value) / pow(($registro->dewptout + 237.7) , 2));
                                $deltaT = $registro->tempout - round($bulboHumedoMin,3);
                                $deltaT = round($deltaT,3);
                            } catch (\Exception $e) {
                                Log::warning("Error al calcular bulbo húmedo en alarm de estación {$registro->station_id}: " . $e->getMessage());
                            }

                            if(!is_null($registro->windspeed) || !is_null($registro->windspeed2)){
                                $viento = $registro->windspeed !=null? $registro->windspeed:$registro->windspeed2;
                                if($viento!=null){
                                    if ((round($viento) <= 10 && (round($deltaT) < 2 || round($deltaT) > 8)) || (round($viento) > 10 && round($viento) <= 15 && (round($deltaT) < 2 || round($deltaT) > 6)) || (round($viento) > 15 && (round($deltaT) < 0 || round($deltaT) > 2))) {
                                        $listAlarm[] = [
                                            'station_id' => $registro->station_id,
                                            'user_id' => $registro->user_id,
                                            'reg_date' => $registro->receipt_date,
                                            'value' => $deltaT,
                                            'variable' => "delta T",
                                            'unit' => "°C",
                                            'state' => 1,
                                            'created_at' => now(),
                                            'updated_at' => now(),
                                        ];
                                    }
                                } 
                            }                          
                            
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning("Error al insertar registro alarm de estación {$registro->station_id}: " . $e->getMessage());
                    continue;
                }
            }
            if(count($listAlarm) > 0){
                AlarmStoreM::insert($listAlarm);
            }
        });
        


        self::dispatchStoreNotifications('alarm_store', 'Notificación de Alarmas');

    }

    public static function runAlert(){
        date_default_timezone_set('America/La_Paz');
        DB::table('alert_store')->truncate();
        DB::table(DB::raw("
            (
                SELECT 
                    alerts_alarms.temp_max,
                    alerts_alarms.temp_min,
                    alerts_alarms.dewpoint,
                    alerts_alarms.wind_max,
                    alerts_alarms.rain_total,
                    alerts_alarms.uv_max,
                    alerts_alarms.solar_rad_max,
                    alerts_alarms.dt_max,
                    alerts_alarms.dt_min,
                    alerts_alarms.type,
                    f.id,
                    f.reg_date as receipt_date,
                    f.t2m as tempout,
                    f.v10m as windspeedhi,
                    f.hr2m as humout,
                    f.prec as raintotal,
                    alerts_alarms.station_id,
                    alerts_alarms.user_id,
                    ROW_NUMBER() OVER (
                        PARTITION BY alerts_alarms.user_id, alerts_alarms.station_id 
                        ORDER BY f.reg_date ASC
                    ) as row_num
                FROM alerts_alarms
                INNER JOIN stations as s ON alerts_alarms.station_id = s.id
                INNER JOIN forecasts as f ON f.station_id = s.id
                WHERE 
                    alerts_alarms.notification_status = 1 AND
                    alerts_alarms.state = 1 AND
                    alerts_alarms.type = 'alert' AND
                    s.state = 1 AND
                    f.reg_date > NOW() AND

                    (
                        alerts_alarms.temp_min IS NULL OR alerts_alarms.temp_max IS NULL OR
                        (f.t2m IS NOT NULL AND ROUND(f.t2m) BETWEEN ROUND(alerts_alarms.temp_min) AND ROUND(alerts_alarms.temp_max))
                    ) AND

                    (
                        alerts_alarms.wind_max IS NULL OR
                        (f.v10m IS NOT NULL AND ROUND(alerts_alarms.wind_max) >= ROUND(f.v10m))
                    ) AND

                    (
                        alerts_alarms.rain_total IS NULL OR
                        (f.prec IS NOT NULL AND ROUND(alerts_alarms.rain_total) >= ROUND(f.prec))
                    ) AND

                    (
                        alerts_alarms.dewpoint IS NULL OR
                        (f.dp IS NOT NULL AND TRUNCATE(alerts_alarms.dewpoint, 1) >= TRUNCATE(f.dp, 1))
                    )
            ) as sub
        "))
        ->where('row_num', '<=',15)
        ->orderBy('user_id')
        ->orderBy('station_id')
        //->orderBy('receipt_date')
        ->chunk(100, function ($registros) { 
            //dd($registros);           
            $listAlert = [];
            foreach ($registros as $registro) {
                try {
                    
                    if($registro->type=="alert"){
                        
                        if(!is_null($registro->tempout) && !is_null($registro->temp_max) && round($registro->tempout) >= round($registro->temp_max)){
                            $listAlert[] = [
                                'station_id' => $registro->station_id,
                                'user_id' => $registro->user_id,
                                'reg_date' => $registro->receipt_date,
                                'value' => $registro->tempout,
                                'variable' => "temperatura",
                                'unit' => "°C",
                                'state' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }

                        if(!is_null($registro->tempout) && !is_null($registro->temp_min) && round($registro->tempout) <= round($registro->temp_min)){
                            $listAlert[] = [
                                'station_id' => $registro->station_id,
                                'user_id' => $registro->user_id,
                                'reg_date' => $registro->receipt_date,
                                'value' => $registro->tempout,
                                'variable' => "temperatura",
                                'unit' => "°C",
                                'state' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                        

                        if(!is_null($registro->tempout) && !is_null($registro->humout) ){
                            if (round($registro->humout) >= round($registro->tempout) - 2) { //"⚠️ Punto de Rocío Alto" (formula excel);                                
                                $listAlarm[] = [
                                    'station_id' => $registro->station_id,
                                    'user_id' => $registro->user_id,
                                    'reg_date' => $registro->receipt_date,
                                    'value' => $registro->dewpoint,
                                    'variable' => "punto_rocio",
                                    'unit' => "°C",
                                    'state' => 1,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                        }

                        if(!is_null($registro->wind_max) && !is_null($registro->windspeedhi) && round($registro->wind_max) >= round($registro->windspeedhi) ){
                            $listAlert[] = [
                                'station_id' => $registro->station_id,
                                'user_id' => $registro->user_id,
                                'reg_date' => $registro->receipt_date,
                                'value' => $registro->windspeedhi,
                                'variable' => "viento",
                                'unit' => "km/h",
                                'state' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }

                        if(!is_null($registro->rain_total) && !is_null($registro->raintotal) && round($registro->rain_total) >= round($registro->raintotal) ){
                            $listAlert[] = [
                                'station_id' => $registro->station_id,
                                'user_id' => $registro->user_id,
                                'reg_date' => $registro->receipt_date,
                                'value' => $registro->raintotal,
                                'variable' => "lluvia",
                                'unit' => "mm",
                                'state' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }

                    }

                } catch (\Exception $e) {
                    Log::warning("Error al insertar registro alert de estación {$registro->station_id}: " . $e->getMessage());
                    continue;
                }
            }
            if(count($listAlert) > 0){
                AlertStoreM::insert($listAlert);
            }
        });

        self::dispatchStoreNotifications('alert_store', 'Notificación de Alertas');

    }

    // Agrupa los registros generados por usuario y envía un resumen móvil por ejecución.
    private static function dispatchStoreNotifications(string $tableName, string $title): void
    {
        $currentUserId = null;
        $description = '';
        $isFirst = true;

        DB::table($tableName . ' as store')
            ->join('users as u', 'u.id', '=', 'store.user_id')
            ->where('u.state', 1)
            ->where('store.state', 1)
            ->select('u.id', 'store.id as store_id', 'store.reg_date', 'store.value', 'store.variable', 'store.unit')
            ->orderBy('u.id')
            ->orderBy('store.id')
            ->lazy()
            ->each(function (object $item) use (&$currentUserId, &$description, &$isFirst, $title) {
                date_default_timezone_set('America/La_Paz');

                if ($isFirst) {
                    $currentUserId = $item->id;
                    $isFirst = false;
                }

                if ($currentUserId !== $item->id) {
                    self::dispatchUserPush($currentUserId, $title, $description);
                    $currentUserId = $item->id;
                    $description = '';
                }

                $fecha = Carbon::parse($item->reg_date)->format('m/d H:i') . ' Hrs';
                $description .= ' | ' . $item->variable . ' ' . $item->value . ' ' . $item->unit . '  -  ' . $fecha;
                $description = trim($description, ' | ');
            });

        if (!$isFirst && $currentUserId !== null && $description !== '') {
            self::dispatchUserPush($currentUserId, $title, $description);
        }
    }

    // Envía el resumen consolidado al usuario aprovechando todos sus tokens móviles activos.
    private static function dispatchUserPush(int $userId, string $title, string $description): void
    {
        try {
            app(PushNotificationService::class)->sendToUsers(
                [$userId],
                'mobile',
                $title,
                mb_substr($description, 0, 180, 'UTF-8')
            );
        } catch (\Throwable $th) {
            Log::info('Error enviando notificación automática Firebase: ' . $th->getMessage(), [
                'user_id' => $userId,
                'title' => $title,
            ]);
        }
    }
    
}
