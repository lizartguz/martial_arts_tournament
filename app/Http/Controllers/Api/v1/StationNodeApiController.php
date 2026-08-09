<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\CurrentDataM;
use App\Models\NetworkCredentialHistoryM;
use App\Models\NetworkCredentialM;
use App\Models\StationM;
use App\Services\StationCalibrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StationNodeApiController extends Controller
{
    private const MEASUREMENT_FIELDS = [
        'tempout', 'tempin', 'humout', 'humin', 'humoutmax', 'humoutmin',
        'dewptout', 'heatindexout', 'dewptin', 'press', 'seapress', 'windavg',
        'winddir', 'windchill', 'windspeed', 'windspeedhi', 'winddirhi',
        'windspeed2', 'winddir2', 'windspeedhi2', 'winddirhi2',
        'rainrate', 'raintotal', 'uvindex', 'solrad', 'solradhi', 'solevo',
        'pm1', 'pm2_5', 'pm10', 'gas_ppm',
        'soiltemp1', 'soiltemp2', 'soiltemp3', 'soiltemp4',
        'soilhum1', 'soilhum2', 'soilhum3', 'soilhum4',
        'leaftemp1', 'leaftemp2', 'leaftemp3', 'leaftemp4',
        'leafhum1', 'leafhum2', 'leafhum3', 'leafhum4',
    ];

    public function __construct(protected StationCalibrationService $stationCalibrationService)
    {
    }

    /**
     * Entrega las credenciales de red Wi-Fi al Nodo.
     */
    public function getNetwork(Request $request)
    {
        try {
            $code = $request->query('code');

            if (empty($code)) {
                Log::warning('[NodeAPI] getNetwork: Peticion sin codigo de estacion');

                return response()->json(['error' => 'El codigo de estacion es requerido.'], 400);
            }

            Log::info("[NodeAPI] getNetwork: Buscando credenciales para: {$code}");

            $station = StationM::where('code', $code)
                ->where('state', 1)
                ->first();

            if (!$station) {
                Log::warning("[NodeAPI] getNetwork: Estacion no encontrada o inactiva: {$code}");

                return response()->json(['error' => 'Estacion no encontrada o inactiva.'], 404);
            }

            $credential = NetworkCredentialM::where('station_id', $station->id)
                ->where('state', 1)
                ->where('sent_to_device', 1)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$credential) {
                Log::info("[NodeAPI] getNetwork: Sin actualizaciones pendientes para ID: {$station->id}");

                return response()->json(['error' => 'Sin actualizaciones pendientes'], 404);
            }

            NetworkCredentialHistoryM::create([
                'ssid' => $credential->ssid,
                'password' => $credential->password,
                'state' => $credential->state,
                'network_credential_id' => $credential->id,
                'user_creator_id' => $credential->user_creator_id,
            ]);

            $credential->update(['sent_to_device' => 0]);

            Log::info("[NodeAPI] getNetwork: Credenciales entregadas y bandera reseteada para ID: {$station->id}");

            return response()->json([
                'ssid' => $credential->ssid,
                'password' => $credential->password,
            ], 200);
        } catch (\Exception $e) {
            Log::error('[NodeAPI] getNetwork Error: ' . $e->getMessage());

            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Recibe y persiste mediciones.
     */
    public function receiveStationData(Request $request)
    {
        date_default_timezone_set('America/La_Paz');
        $result = 'failed';
        $year = date('Y');

        try {
            $code = $request->input('code');
            $fechaInput = $request->input('collection_date');

            if (empty($code) || empty($fechaInput)) {
                Log::warning('[NodeAPI] receiveData: Parametros basicos faltantes (code/date)');

                return response('failed', 200)->header('Content-Type', 'text/plain');
            }

            $timestamp = strtotime($fechaInput);
            if ($timestamp === false) {
                throw new \Exception("Fecha invalida: {$fechaInput}");
            }

            $receipt_date = date('Y-m-d H:i:s', $timestamp);
            $registration_date = date('Y-m-d H:i:s');
            $year = date('Y', $timestamp);

            $extracted_data = [];
            foreach (self::MEASUREMENT_FIELDS as $param) {
                $extracted_data[$param] = $this->numericInputOrNull($request, $param);
            }

            $station = StationM::select('id', 'name', 'et')
                ->where('code', $code)
                ->where('state', 1)
                ->first();

            if (!$station) {
                throw new \Exception("Estacion no encontrada: {$code}");
            }

            $station_id = (int) $station->id;
            $extracted_data = $this->stationCalibrationService->applyToPayload($station_id, $extracted_data);

            $temp = $extracted_data['tempout'];
            $reqMaxTemp = $this->numericInputOrNull($request, 'tempoutmax')
                ?? $this->numericInputOrNull($request, 'maxtempout');
            $reqMinTemp = $this->numericInputOrNull($request, 'tempoutmin')
                ?? $this->numericInputOrNull($request, 'mintempout');

            $tempMax = $reqMaxTemp ?? ($temp !== null ? $temp + 0.1 : null);
            $tempMin = $reqMinTemp ?? ($temp !== null ? $temp - 0.1 : null);

            $solevo = null;
            if ((int) $station->et === 1) {
                $etRow = DB::selectOne(
                    'SELECT ET_CALCULATION(?, ?, ?, ?, ?, ?, ?, ?, ?) AS V_ET',
                    [
                        $station_id,
                        $receipt_date,
                        $temp,
                        $tempMax,
                        $tempMin,
                        $extracted_data['humout'],
                        ($extracted_data['windspeed'] ?? $extracted_data['windspeed2']),
                        $extracted_data['press'],
                        $extracted_data['solrad'],
                    ]
                );

                $solevo = $etRow ? $etRow->V_ET : null;
            }

            $data_to_save = array_merge($extracted_data, [
                'station_id' => $station_id,
                'receipt_date' => $receipt_date,
                'registration_date' => $registration_date,
                'tempoutmax' => $tempMax,
                'tempoutmin' => $tempMin,
                'solevo' => $solevo,
                'state' => 1,
                'created_at' => $registration_date,
                'updated_at' => $registration_date,
            ]);

            DB::beginTransaction();
            DB::connection('senvatec_db_' . $year)->beginTransaction();

            CurrentDataM::upsert([$data_to_save], ['station_id'], array_keys($data_to_save));

            $yearly_data = array_slice($data_to_save, 0, -2, true);
            DB::connection('senvatec_db_' . $year)->table('senva_data')->upsert(
                [$yearly_data],
                ['station_id', 'receipt_date'],
                array_keys($yearly_data)
            );

            DB::connection('senvatec_db_' . $year)->commit();
            DB::commit();

            $pendingUpdate = NetworkCredentialM::where('station_id', $station_id)
                ->where('state', 1)
                ->where('sent_to_device', 1)
                ->exists();

            $result = $pendingUpdate ? 'senvatec_network' : 'senvatec';

            Log::info("[NodeAPI] Data procesada OK. Station: {$station_id}, Result: {$result}");
        } catch (\Exception $e) {
            DB::rollBack();

            try {
                DB::connection('senvatec_db_' . $year)->rollBack();
            } catch (\Exception $err) {
            }

            Log::error('[NodeAPI] Error Ingestion: ' . $e->getMessage());
            $result = 'failed';
        }

        return response($result, 200)->header('Content-Type', 'text/plain');
    }

    private function inputOrNull($request, $key)
    {
        $value = $request->input($key);
        if ($value === null) {
            return null;
        }

        $value = trim($value);
        if ($value === '' || strtolower($value) === 'null') {
            return null;
        }

        return $value;
    }

    private function numericInputOrNull(Request $request, string $key): ?float
    {
        $value = $this->inputOrNull($request, $key);

        return is_numeric($value) ? (float) $value : null;
    }
}
