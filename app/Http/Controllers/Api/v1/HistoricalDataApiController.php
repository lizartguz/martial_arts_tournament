<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoricalDataApiController extends Controller
{
    /**
     * Últimos 30 registros de datos históricos para una estación desde la BD anual.
     * Conexión: senvatec_db_{year}, tabla senva_data.
     */
    public function getStationHistoricalData(Request $request)
    {
        try {
            $userPermission = UserPermissionVerif::verif(
                $request->userId,
                $request->email,
                $request->phone,
                $request->identifier
            );

            if ($userPermission[0] !== 'yes') {
                return response()->json([
                    'response' => false,
                    'failed'   => 'Usuario sin permisos',
                    'columns'  => [],
                    'data'     => [],
                    'data_user' => $userPermission[3] ?? null,
                    'user_permission' => $userPermission[0] ?? 'no',
                    'permit_detail' => $userPermission[1] ?? '',
                    'type_subscriptions' => $userPermission[2] ?? null,
                ], 403);
            }

            $stationId = $request->input('station_id');
            if (empty($stationId)) {
                return response()->json([
                    'response' => false,
                    'failed'   => 'station_id es requerido',
                    'columns'  => [],
                    'data'     => [],
                    'data_user' => $userPermission[3] ?? null,
                    'user_permission' => $userPermission[0] ?? 'no',
                    'permit_detail' => $userPermission[1] ?? '',
                    'type_subscriptions' => $userPermission[2] ?? null,
                ], 400);
            }

            $year = (int) ($request->input('year') ?? date('Y'));
            $connectionName = 'senvatec_db_' . $year;

            // Control de variables para la app movil:
            // - visible => true: la variable se consulta, se envia y se muestra
            //   en la tabla, PDF y Excel.
            // - visible => false: la variable no se consulta ni se envia, por tanto
            //   no aparece en la app movil ni en las exportaciones.
            // - Si una variable visible se envia con valor null, la columna aparece
            //   y la app mostrara la celda vacia.
            $availableColumns = [
                ['key' => 'station_id', 'label' => 'Estacion', 'visible' => false],
                ['key' => 'receipt_date', 'label' => 'Fecha', 'visible' => true],
                ['key' => 'tempout', 'label' => 'Temp °C', 'visible' => true],
                ['key' => 'tempoutmin', 'label' => 'T.min °C', 'visible' => true],
                ['key' => 'tempoutmax', 'label' => 'T.max °C', 'visible' => true],
                ['key' => 'humout', 'label' => 'Hum %', 'visible' => true],
                ['key' => 'humoutmin', 'label' => 'Hum.min %', 'visible' => true],
                ['key' => 'humoutmax', 'label' => 'Hum.max %', 'visible' => true],
                ['key' => 'dewptout', 'label' => 'P.rocio °C', 'visible' => true],
                ['key' => 'heatindexout', 'label' => 'Ind.calor °C', 'visible' => false],
                ['key' => 'press', 'label' => 'Presion hPa', 'visible' => true],
                ['key' => 'seapress', 'label' => 'P.mar hPa', 'visible' => false],
                ['key' => 'windchill', 'label' => 'Sens. °C', 'visible' => false],
                ['key' => 'windavg', 'label' => 'V.prom m/s', 'visible' => false],
                ['key' => 'windspeed', 'label' => 'Viento m/s', 'visible' => true],
                ['key' => 'winddir', 'label' => 'Dir °', 'visible' => true],
                ['key' => 'windspeedhi', 'label' => 'Racha m/s', 'visible' => false],
                ['key' => 'winddirhi', 'label' => 'DirR °', 'visible' => false],
                ['key' => 'windspeed2', 'label' => 'V2 m/s', 'visible' => false],
                ['key' => 'winddir2', 'label' => 'Dir2 °', 'visible' => false],
                ['key' => 'windspeedhi2', 'label' => 'R2 m/s', 'visible' => false],
                ['key' => 'winddirhi2', 'label' => 'DirR2 °', 'visible' => false],
                ['key' => 'rainrate', 'label' => 'Lluvia mm/h', 'visible' => true],
                ['key' => 'raintotal', 'label' => 'Lluvia mm', 'visible' => true],
                ['key' => 'uvindex', 'label' => 'UV', 'visible' => true],
                ['key' => 'solrad', 'label' => 'Rad W/m²', 'visible' => true],
                ['key' => 'solradhi', 'label' => 'Rad.max W/m²', 'visible' => false],
                ['key' => 'solevo', 'label' => 'ETo mm', 'visible' => true],
                ['key' => 'pm1', 'label' => 'PM1 µg/m³', 'visible' => true],
                ['key' => 'pm2_5', 'label' => 'PM2.5 µg/m³', 'visible' => true],
                ['key' => 'pm10', 'label' => 'PM10 µg/m³', 'visible' => true],
                ['key' => 'gas_ppm', 'label' => 'Gas ppm', 'visible' => false],
            ];

            $enabledColumns = collect($availableColumns)
                ->filter(fn ($column) => ($column['visible'] ?? true) === true)
                ->values();

            $columns = $enabledColumns
                ->pluck('key')
                ->all();

            $visibleColumns = $enabledColumns
                ->map(fn ($column) => [
                    'key' => $column['key'],
                    'label' => $column['label'],
                ])
                ->values()
                ->all();

            $rows = DB::connection($connectionName)
                ->table('senva_data')
                ->where('station_id', $stationId)
                ->orderBy('receipt_date', 'desc')
                ->limit(30)
                ->get($columns);

            $data = $rows->map(function ($row) {
                return (array) $row;
            })->values()->all();

            return response()->json([
                'response' => true,
                'failed'   => null,
                'columns'  => $visibleColumns,
                'data'     => $data,
                'data_user' => $userPermission[3] ?? null,
                'user_permission' => $userPermission[0] ?? 'yes',
                'permit_detail' => $userPermission[1] ?? '',
                'type_subscriptions' => $userPermission[2] ?? null,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'response' => false,
                'failed'   => $e->getMessage(),
                'columns'  => [],
                'data'     => [],
                'data_user' => null,
                'user_permission' => 'no',
                'permit_detail' => '',
                'type_subscriptions' => null,
            ], 500);
        }
    }
}
