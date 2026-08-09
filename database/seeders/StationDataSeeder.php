<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\StationM;
use Carbon\Carbon;

/**
 * StationDataSeeder
 * -----------------------------------------------------------------------------
 * Seeder "fresco" de datos meteorologicos. Cada vez que se ejecuta:
 *
 *   1. LIMPIA (TRUNCATE) las dos tablas involucradas:
 *        - current_data            (BD principal)
 *        - senva_data              (BD historica por anio: senvatec_db_<anio>)
 *
 *   2. Inserta 1 registro ACTUAL por estacion en current_data
 *      (fecha = momento de ejecucion, date()).
 *
 *   3. Inserta N registros HISTORICOS por estacion en senva_data, espaciados
 *      cada 10 minutos hacia atras. El mas reciente es el momento actual y los
 *      anteriores retroceden 10 min cada uno. El anio de la conexion se calcula
 *      dinamicamente desde el timestamp de cada registro, igual que en
 *      StationNodeApiController::receiveStationData().
 *
 * Ejecutar de forma aislada:
 *   php artisan db:seed --class=StationDataSeeder
 */
class StationDataSeeder extends Seeder
{
    /** Cantidad de registros historicos por estacion (el mas reciente = ahora). */
    private const RECORDS_PER_STATION = 20;

    /** Minutos de separacion entre cada registro historico. */
    private const INTERVAL_MINUTES = 10;

    /** Estaciones cuyo ultimo dato queda exactamente hace dos semanas. */
    private const OFFLINE_TWO_WEEKS_AGO = [
        'Caida Dos Semanas 01',
        'Caida Dos Semanas 02',
        'Caida Dos Semanas 03',
        'Caida Dos Semanas 04',
        'Caida Dos Semanas 05',
        'Caida Dos Semanas 06',
        'Caida Dos Semanas 07',
        'Caida Dos Semanas 08',
        'Caida Dos Semanas 09',
        'Caida Dos Semanas 10',
    ];

    /** Estaciones cuyo ultimo dato queda mas atras de dos semanas. */
    private const OFFLINE_MORE_THAN_TWO_WEEKS_AGO = [
        'Caida Mas Dos Semanas 01',
        'Caida Mas Dos Semanas 02',
        'Caida Mas Dos Semanas 03',
        'Caida Mas Dos Semanas 04',
        'Caida Mas Dos Semanas 05',
        'Caida Mas Dos Semanas 06',
        'Caida Mas Dos Semanas 07',
        'Caida Mas Dos Semanas 08',
        'Caida Mas Dos Semanas 09',
        'Caida Mas Dos Semanas 10',
    ];

    /**
     * Filas por lote en cada upsert. Evita el limite de MySQL de 65.535
     * placeholders por sentencia (57 columnas * 500 = 28.500 placeholders).
     */
    private const UPSERT_CHUNK = 500;

    public function run(): void
    {
        // Misma zona horaria que usa la ingestion real del nodo.
        date_default_timezone_set('America/La_Paz');

        $stations = StationM::select('id', 'name', 'latitude', 'longitude')
            ->orderBy('id')
            ->get();

        if ($stations->isEmpty()) {
            echo "⚠️  No se encontraron estaciones. Ejecuta primero el seeder de estaciones.\n";
            return;
        }

        $now = Carbon::now();

        echo "🌦️  StationDataSeeder\n";
        echo "📊 Estaciones: {$stations->count()}\n";
        echo "📝 Historicos por estacion: " . self::RECORDS_PER_STATION
            . " (cada " . self::INTERVAL_MINUTES . " min)\n";
        echo "🕒 Referencia (ahora): {$now->format('Y-m-d H:i:s')}\n\n";

        // ---------------------------------------------------------------------
        // 1) Construir timestamps historicos por estacion. Las estaciones de
        //    prueba quedan con su ultimo dato atrasado para simular caidas.
        // ---------------------------------------------------------------------
        $timestampsByStation = [];
        $allTimestamps = [];

        foreach ($stations as $station) {
            $latestReceiptDate = $this->getLatestReceiptDate($station->name, $now);

            for ($i = 0; $i < self::RECORDS_PER_STATION; $i++) {
                $receiptDate = $latestReceiptDate->copy()->subMinutes($i * self::INTERVAL_MINUTES);
                $timestampsByStation[$station->id][] = $receiptDate;
                $allTimestamps[] = $receiptDate;
            }
        }

        // Anios distintos que tocan los historicos (incluye estaciones caidas).
        $years = collect($allTimestamps)
            ->map(fn (Carbon $t) => $t->format('Y'))
            ->unique()
            ->values();

        // ---------------------------------------------------------------------
        // 2) LIMPIEZA: truncar ambas tablas antes de re-poblar (datos frescos).
        // ---------------------------------------------------------------------
        echo "🧹 Limpiando tablas...\n";
        $this->truncateCurrentData();

        foreach ($years as $year) {
            $connection = 'senvatec_db_' . $year;
            $this->ensureYearDatabaseExists($connection);
            $this->truncateSenvaData($connection);
        }
        echo "\n";

        // ---------------------------------------------------------------------
        // 3) current_data: un unico registro (el actual) por estacion.
        // ---------------------------------------------------------------------
        $currentRows = [];
        foreach ($stations as $station) {
            // Para estaciones caidas, el registro actual queda con fecha antigua.
            $receiptDate = $timestampsByStation[$station->id][0];
            $registrationDate = $this->isOfflineStation($station->name) ? $receiptDate : $now;
            $rowTimestamp = $registrationDate->format('Y-m-d H:i:s');
            $reading = $this->buildReading($station, $receiptDate, $registrationDate);

            $currentRows[] = array_merge($reading, [
                'station_id' => $station->id,
                'created_at' => $rowTimestamp,
                'updated_at' => $rowTimestamp,
            ]);
        }

        $currentUpdateColumns = array_keys($currentRows[0]);
        foreach (array_chunk($currentRows, self::UPSERT_CHUNK) as $chunk) {
            DB::table('current_data')->upsert(
                $chunk,
                ['station_id'],             // garantiza 1 registro por estacion
                $currentUpdateColumns
            );
        }

        echo "✅ current_data: " . count($currentRows) . " registros (1 por estacion).\n";

        // ---------------------------------------------------------------------
        // 4) senva_data: N registros historicos por estacion, agrupados por la
        //    conexion del anio correspondiente al receipt_date.
        // ---------------------------------------------------------------------
        $rowsByConnection = [];

        foreach ($stations as $station) {
            foreach ($timestampsByStation[$station->id] as $receiptDate) {
                $connection = 'senvatec_db_' . $receiptDate->format('Y');

                // Las estaciones caidas conservan registration_date antiguo.
                $registrationDate = $this->isOfflineStation($station->name) ? $receiptDate : $now;
                $rowTimestamp = $registrationDate->format('Y-m-d H:i:s');
                $reading = $this->buildReading($station, $receiptDate, $registrationDate);

                $rowsByConnection[$connection][] = array_merge($reading, [
                    'station_id' => $station->id,
                    'created_at' => $rowTimestamp,
                    'updated_at' => $rowTimestamp,
                ]);
            }
        }

        $totalHistoricos = 0;
        foreach ($rowsByConnection as $connection => $rows) {
            $updateColumns = array_keys($rows[0]);

            DB::connection($connection)->transaction(function () use ($connection, $rows, $updateColumns) {
                // Se inserta por lotes para no exceder el limite de placeholders de MySQL.
                foreach (array_chunk($rows, self::UPSERT_CHUNK) as $chunk) {
                    DB::connection($connection)->table('senva_data')->upsert(
                        $chunk,
                        ['station_id', 'receipt_date'],
                        $updateColumns
                    );
                }
            });

            $totalHistoricos += count($rows);
            echo "✅ {$connection}.senva_data: " . count($rows) . " registros.\n";
        }

        echo "\n🎉 Listo. current_data: " . count($currentRows)
            . " | historicos: {$totalHistoricos}\n";
        $oldestReceiptDate = collect($allTimestamps)->min(fn (Carbon $t) => $t->timestamp);
        $latestReceiptDate = collect($allTimestamps)->max(fn (Carbon $t) => $t->timestamp);
        echo "📅 Rango historico: "
            . Carbon::createFromTimestamp($oldestReceiptDate)->format('Y-m-d H:i:s')
            . " → " . Carbon::createFromTimestamp($latestReceiptDate)->format('Y-m-d H:i:s') . "\n";
    }

    /**
     * Define la fecha del ultimo dato para estaciones activas o caidas.
     */
    private function getLatestReceiptDate(string $stationName, Carbon $now): Carbon
    {
        if (in_array($stationName, self::OFFLINE_TWO_WEEKS_AGO, true)) {
            return $now->copy()->subWeeks(2);
        }

        if (in_array($stationName, self::OFFLINE_MORE_THAN_TWO_WEEKS_AGO, true)) {
            return $now->copy()->subDays(21);
        }

        return $now->copy();
    }

    /**
     * Identifica estaciones que deben sembrarse sin datos recientes.
     */
    private function isOfflineStation(string $stationName): bool
    {
        return in_array($stationName, self::OFFLINE_TWO_WEEKS_AGO, true)
            || in_array($stationName, self::OFFLINE_MORE_THAN_TWO_WEEKS_AGO, true);
    }

    /**
     * Trunca current_data (desactivando FKs por seguridad).
     */
    private function truncateCurrentData(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE current_data');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        echo "   • current_data truncada.\n";
    }

    /**
     * Trunca senva_data en la conexion del anio indicado (si la tabla existe).
     */
    private function truncateSenvaData(string $connection): void
    {
        if (!Schema::connection($connection)->hasTable('senva_data')) {
            echo "   • {$connection}.senva_data no existe aun, se omite el truncado.\n";
            return;
        }

        DB::connection($connection)->statement('SET FOREIGN_KEY_CHECKS=0');
        DB::connection($connection)->statement('TRUNCATE TABLE senva_data');
        DB::connection($connection)->statement('SET FOREIGN_KEY_CHECKS=1');

        echo "   • {$connection}.senva_data truncada.\n";
    }

    /**
     * Garantiza que la base de datos del anio exista (replica la logica del
     * SenvaDataSeeder original para entornos donde la BD anual aun no se creo).
     */
    private function ensureYearDatabaseExists(string $connection): void
    {
        if (!config("database.connections.{$connection}")) {
            echo "   • Conexion '{$connection}' no configurada, se omite.\n";
            return;
        }

        $dbName = config("database.connections.{$connection}.database");

        $serverConfig = config("database.connections.{$connection}");
        $serverConfig['database'] = null;
        config(["database.connections.server_{$connection}" => $serverConfig]);

        DB::connection("server_{$connection}")
            ->statement("CREATE DATABASE IF NOT EXISTS `{$dbName}`");
    }

    /**
     * Construye un registro meteorologico realista (ficticio) para una estacion
     * en una fecha dada. La estructura de columnas es compatible tanto con
     * current_data como con senva_data.
     *
     * @param  object  $station          Estacion (id, name, latitude, longitude)
     * @param  Carbon  $receiptDate       Fecha de la medicion
     * @param  Carbon  $registrationDate  Fecha de registro (normalmente "ahora")
     */
    private function buildReading($station, Carbon $receiptDate, Carbon $registrationDate): array
    {
        $latitude = (float) $station->latitude;

        // Rangos base segun la franja latitudinal (Bolivia, simplificado).
        if ($latitude < -20) {
            // Sur (mas frio y seco)
            $tempRange = [8, 25];
            $humRange = [30, 70];
            $rainChance = 0.15;
        } elseif ($latitude > -16) {
            // Norte / tropical (mas calido y humedo)
            $tempRange = [18, 32];
            $humRange = [50, 90];
            $rainChance = 0.35;
        } else {
            // Centro
            $tempRange = [12, 28];
            $humRange = [40, 80];
            $rainChance = 0.25;
        }

        // Ajuste de temperatura segun la hora del dia.
        $hour = $receiptDate->hour;
        if ($hour >= 12 && $hour <= 16) {
            $tempAdjustment = 5;   // pico de calor
        } elseif ($hour >= 0 && $hour <= 6) {
            $tempAdjustment = -5;  // madrugada
        } elseif ($hour >= 7 && $hour <= 11) {
            $tempAdjustment = 2;   // manana
        } else {
            $tempAdjustment = -2;  // tarde/noche
        }

        $tempout = round($this->randomFloat($tempRange[0] + $tempAdjustment, $tempRange[1] + $tempAdjustment), 2);
        $humout = round($this->randomFloat($humRange[0], $humRange[1]), 2);
        $press = round($this->randomFloat(1010, 1025), 2);

        $tempoutmin = round($tempout - $this->randomFloat(0.5, 2), 2);
        $tempoutmax = round($tempout + $this->randomFloat(0.5, 3), 2);

        $humoutmin = max(0, round($humout - $this->randomFloat(2, 10), 2));
        $humoutmax = min(100, round($humout + $this->randomFloat(2, 10), 2));

        // Punto de rocio (aproximacion).
        $dewptout = round($tempout - ((100 - $humout) / 5), 2);

        // Viento (mayor durante el dia).
        $windspeed = ($hour >= 10 && $hour <= 18)
            ? round($this->randomFloat(5, 25), 2)
            : round($this->randomFloat(1, 12), 2);
        $winddir = fmod(round($this->randomFloat(0, 359), 2) + 360, 360);
        $windspeedhi = round($windspeed + $this->randomFloat(3, 10), 2);

        // Sensor de viento 2 (con ligera variacion).
        $windspeed2 = max(0, round($windspeed + $this->randomFloat(-2, 2), 2));
        $winddir2 = fmod(round($winddir + $this->randomFloat(-30, 30), 2) + 360, 360);
        $windspeedhi2 = round($windspeed2 + $this->randomFloat(3, 10), 2);

        // Precipitacion (probabilistica).
        $raintotal = (rand(0, 100) / 100) < $rainChance
            ? round($this->randomFloat(0, 5), 2)
            : 0;
        $rainrate = $raintotal > 0
            ? round($this->randomFloat(0, $raintotal), 2)
            : 0;

        // Radiacion solar / UV (solo de dia).
        $solrad = ($hour >= 6 && $hour <= 18)
            ? round($this->randomFloat(200, 1000), 2)
            : 0;
        $solradhi = $solrad > 0
            ? round($solrad + $this->randomFloat(50, 200), 2)
            : 0;
        $uvindex = ($hour >= 10 && $hour <= 16)
            ? round($this->randomFloat(5, 12), 2)
            : round($this->randomFloat(0, 4), 2);

        // Sensores de suelo.
        $soiltemp1 = round($this->randomFloat($tempout - 5, $tempout + 3), 2);
        $soilhum1 = round($this->randomFloat(20, 60), 2);

        // Sensores de hoja.
        $leaftemp1 = round($this->randomFloat($tempout - 3, $tempout + 2), 2);
        $leafhum1 = round($this->randomFloat(40, 90), 2);

        // Calidad del aire (no todas las estaciones la reportan).
        $pm10 = (rand(0, 100) / 100) < 0.3 ? round($this->randomFloat(10, 50), 2) : null;
        $pm2_5 = $pm10 !== null ? round($pm10 * 0.6, 2) : null;
        $pm1 = $pm2_5 !== null ? round($pm2_5 * 0.5, 2) : null;
        $gas_ppm = (rand(0, 100) / 100) < 0.3 ? round($this->randomFloat(50, 500), 2) : null;

        return [
            'receipt_date' => $receiptDate->format('Y-m-d H:i:s'),
            'registration_date' => $registrationDate->format('Y-m-d H:i:s'),

            // Temperatura
            'tempin' => round($tempout + $this->randomFloat(-2, 3), 2),
            'tempout' => $tempout,
            'tempoutmin' => $tempoutmin,
            'tempoutmax' => $tempoutmax,

            // Humedad
            'humin' => max(0, min(100, round($humout + $this->randomFloat(-5, 5), 2))),
            'humout' => $humout,
            'humoutmin' => $humoutmin,
            'humoutmax' => $humoutmax,

            // Punto de rocio / sensacion termica
            'dewptout' => $dewptout,
            'dewptin' => round($dewptout + $this->randomFloat(-1, 1), 2),
            'heatindexout' => round($tempout + $this->randomFloat(0, 3), 2),

            // Presion
            'press' => $press,
            'seapress' => round($press + $this->randomFloat(1, 5), 2),

            // Viento sensor 1
            'windchill' => $tempout < 15 ? round($tempout - $this->randomFloat(1, 5), 2) : $tempout,
            'windavg' => round($windspeed * 0.8, 2),
            'windspeed' => $windspeed,
            'winddir' => round($winddir, 2),
            'windspeedhi' => $windspeedhi,
            'winddirhi' => fmod(round($this->randomFloat(0, 359), 2) + 360, 360),
            'winddirstr' => $this->getWindDirection($winddir),

            // Viento sensor 2
            'windspeed2' => $windspeed2,
            'winddir2' => round($winddir2, 2),
            'windspeedhi2' => $windspeedhi2,
            'winddirhi2' => fmod(round($this->randomFloat(0, 359), 2) + 360, 360),
            'winddirstr2' => $this->getWindDirection($winddir2),

            // Precipitacion
            'rainrate' => $rainrate,
            'raintotal' => $raintotal,

            // Radiacion solar
            'uvindex' => $uvindex,
            'solrad' => $solrad,
            'solradhi' => $solradhi,
            'solevo' => $solrad > 0 ? round($this->randomFloat(0.1, 0.5), 2) : 0,

            // Sensores de suelo
            'soiltemp1' => $soiltemp1,
            'soiltemp2' => round($this->randomFloat($soiltemp1 - 2, $soiltemp1 + 2), 2),
            'soiltemp3' => round($this->randomFloat($soiltemp1 - 3, $soiltemp1 + 3), 2),
            'soiltemp4' => round($this->randomFloat($soiltemp1 - 2, $soiltemp1 + 2), 2),

            'soilhum1' => $soilhum1,
            'soilhum2' => max(0, min(100, round($this->randomFloat($soilhum1 - 10, $soilhum1 + 10), 2))),
            'soilhum3' => max(0, min(100, round($this->randomFloat($soilhum1 - 15, $soilhum1 + 15), 2))),
            'soilhum4' => max(0, min(100, round($this->randomFloat($soilhum1 - 10, $soilhum1 + 10), 2))),

            // Sensores de hoja
            'leaftemp1' => $leaftemp1,
            'leaftemp2' => round($this->randomFloat($leaftemp1 - 2, $leaftemp1 + 2), 2),
            'leaftemp3' => round($this->randomFloat($leaftemp1 - 3, $leaftemp1 + 3), 2),
            'leaftemp4' => round($this->randomFloat($leaftemp1 - 2, $leaftemp1 + 2), 2),

            'leafhum1' => $leafhum1,
            'leafhum2' => max(0, min(100, round($this->randomFloat($leafhum1 - 10, $leafhum1 + 10), 2))),
            'leafhum3' => max(0, min(100, round($this->randomFloat($leafhum1 - 15, $leafhum1 + 15), 2))),
            'leafhum4' => max(0, min(100, round($this->randomFloat($leafhum1 - 10, $leafhum1 + 10), 2))),

            // Calidad del aire
            'pm10' => $pm10,
            'pm2_5' => $pm2_5,
            'pm1' => $pm1,
            'gas_ppm' => $gas_ppm,

            // Estado
            'state' => 1,
        ];
    }

    /**
     * Numero flotante aleatorio entre $min y $max.
     */
    private function randomFloat(float $min, float $max): float
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }

    /**
     * Convierte una direccion de viento en grados a su abreviatura (16 rumbos).
     */
    private function getWindDirection(float $degrees): string
    {
        $degrees = fmod($degrees + 360, 360);

        $directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSO', 'SO', 'OSO', 'O', 'ONO', 'NO', 'NNO'];
        $index = (int) round($degrees / 22.5) % 16;
        $index = max(0, min(15, $index));

        return $directions[$index];
    }
}
