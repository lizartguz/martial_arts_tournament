<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class PrepareNextYearDbCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prepare-next-year-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea la base de datos y tabla senva_data del siguiente año (se activa 30 días antes de fin de año)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = Carbon::now('America/La_Paz');
        $endOfYear = Carbon::create($now->year, 12, 31, 23, 59, 59, 'America/La_Paz');
        $daysRemaining = $now->diffInDays($endOfYear, false);

        Log::info("🗓️  [prepare-next-year-db] Días restantes para fin de año: {$daysRemaining}");

        // Solo actuar si faltan 30 días o menos (un mes antes)
        if ($daysRemaining > 30) {
            $msg = "⏳ [prepare-next-year-db] Aún faltan más de 30 días. No se requiere acción.";
            Log::info($msg);
            $this->info($msg);
            return 0;
        }

        $nextYear = $now->year + 1;
        $dbName = "senvatec_db_{$nextYear}";
        $connectionName = "senvatec_db_{$nextYear}";

        Log::info("🔧 [prepare-next-year-db] Preparando base de datos: {$dbName}");
        $this->info("🔧 Preparando base de datos: {$dbName}");

        try {
            // Registrar dinámicamente la conexión del siguiente año si no existe en config
            $this->registerConnectionIfNeeded($connectionName, $dbName, $now->year);

            // Crear la base de datos si no existe
            $this->createDatabaseIfNeeded($connectionName, $dbName);

            // Reconectar para apuntar a la DB recién creada
            DB::purge($connectionName);

            // Crear la tabla senva_data si no existe
            $this->createSenvaDataTableIfNeeded($connectionName, $dbName);

            $msg = "🎉 [prepare-next-year-db] Base de datos '{$dbName}' lista para recibir datos del año {$nextYear}.";
            Log::info($msg);
            $this->info($msg);

            return 0;
        } catch (\Exception $e) {
            Log::error("❌ [prepare-next-year-db] Error: " . $e->getMessage());
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Registra dinámicamente la conexión del siguiente año si no existe en config.
     */
    private function registerConnectionIfNeeded(string $connectionName, string $dbName, int $currentYear): void
    {
        if (config("database.connections.{$connectionName}")) {
            return;
        }

        $mainHost = config('database.connections.mysql.host');
        $mainPort = config('database.connections.mysql.port');

        // Tomar credenciales de la conexión del año actual como referencia
        $currentYearConn = "senvatec_db_{$currentYear}";
        $refHost = config("database.connections.{$currentYearConn}.host", $mainHost);
        $refPort = config("database.connections.{$currentYearConn}.port", $mainPort);
        $refUser = config("database.connections.{$currentYearConn}.username", 'root');
        $refPass = config("database.connections.{$currentYearConn}.password", '');

        config(["database.connections.{$connectionName}" => [
            'driver'      => 'mysql',
            'host'        => $refHost,
            'port'        => $refPort,
            'database'    => $dbName,
            'username'    => $refUser,
            'password'    => $refPass,
            'unix_socket' => '',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
            'options'     => extension_loaded('pdo_mysql') ? array_filter([
                \PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ]]);

        Log::info("📝 [prepare-next-year-db] Conexión '{$connectionName}' registrada dinámicamente.");
    }

    /**
     * Crea la base de datos en MySQL si no existe.
     */
    private function createDatabaseIfNeeded(string $connectionName, string $dbName): void
    {
        $serverConfig = config("database.connections.{$connectionName}");
        $serverConfig['database'] = null;
        config(["database.connections.server_{$connectionName}" => $serverConfig]);

        DB::connection("server_{$connectionName}")->statement("CREATE DATABASE IF NOT EXISTS `{$dbName}`");
        Log::info("✅ [prepare-next-year-db] Base de datos '{$dbName}' creada/verificada.");
        $this->info("✅ Base de datos '{$dbName}' creada/verificada.");
    }

    /**
     * Crea la tabla senva_data con toda su estructura e índices.
     */
    private function createSenvaDataTableIfNeeded(string $connectionName, string $dbName): void
    {
        if (Schema::connection($connectionName)->hasTable('senva_data')) {
            $msg = "ℹ️  [prepare-next-year-db] La tabla 'senva_data' ya existe en '{$dbName}'. Sin cambios.";
            Log::info($msg);
            $this->info($msg);
            return;
        }

        Schema::connection($connectionName)->create('senva_data', function (Blueprint $table) {
            // Clave primaria compuesta
            $table->unsignedBigInteger('station_id')->comment('ID de la estación');
            $table->timestamp('receipt_date')->comment('Fecha y hora de recepción de datos');

            // Fechas adicionales
            $table->timestamp('registration_date')->nullable()->comment('Fecha de registro');

            // Temperatura interior y exterior
            $table->decimal('tempin', 8, 2)->nullable()->comment('Temperatura interior');
            $table->decimal('tempout', 8, 2)->nullable()->comment('Temperatura exterior');
            $table->decimal('tempoutmin', 8, 2)->nullable()->comment('Temperatura exterior mínima');
            $table->decimal('tempoutmax', 8, 2)->nullable()->comment('Temperatura exterior máxima');

            // Humedad interior y exterior
            $table->decimal('humin', 8, 2)->nullable()->comment('Humedad interior');
            $table->decimal('humout', 8, 2)->nullable()->comment('Humedad exterior');
            $table->decimal('humoutmin', 8, 2)->nullable()->comment('Humedad exterior mínima');
            $table->decimal('humoutmax', 8, 2)->nullable()->comment('Humedad exterior máxima');

            // Punto de rocío y calor
            $table->decimal('dewptout', 8, 2)->nullable()->comment('Punto de rocío exterior');
            $table->decimal('dewptin', 8, 2)->nullable()->comment('Punto de rocío interior');
            $table->decimal('heatindexout', 8, 2)->nullable()->comment('Índice de calor exterior');

            // Presión atmosférica
            $table->decimal('press', 8, 2)->nullable()->comment('Presión atmosférica');
            $table->decimal('seapress', 8, 2)->nullable()->comment('Presión atmosférica a nivel del mar');

            // Viento - Sensor 1
            $table->decimal('windchill', 8, 2)->nullable()->comment('Sensación térmica por viento');
            $table->decimal('windavg', 8, 2)->nullable()->comment('Velocidad promedio del viento');
            $table->decimal('windspeed', 8, 2)->nullable()->comment('Velocidad del viento');
            $table->decimal('winddir', 8, 2)->nullable()->comment('Dirección del viento (grados)');
            $table->decimal('windspeedhi', 8, 2)->nullable()->comment('Ráfaga máxima del viento');
            $table->decimal('winddirhi', 8, 2)->nullable()->comment('Dirección de la ráfaga máxima');
            $table->string('winddirstr', 10)->nullable()->comment('Dirección del viento (texto)');

            // Viento - Sensor 2
            $table->decimal('windspeed2', 8, 2)->nullable()->comment('Velocidad del viento sensor 2');
            $table->decimal('winddir2', 8, 2)->nullable()->comment('Dirección del viento sensor 2 (grados)');
            $table->decimal('windspeedhi2', 8, 2)->nullable()->comment('Ráfaga máxima sensor 2');
            $table->decimal('winddirhi2', 8, 2)->nullable()->comment('Dirección ráfaga máxima sensor 2');
            $table->string('winddirstr2', 10)->nullable()->comment('Dirección del viento sensor 2 (texto)');

            // Precipitación
            $table->decimal('rainrate', 8, 2)->nullable()->comment('Tasa de precipitación');
            $table->decimal('raintotal', 8, 2)->nullable()->comment('Precipitación total acumulada');

            // Radiación solar y UV
            $table->decimal('uvindex', 8, 2)->nullable()->comment('Índice UV');
            $table->decimal('solrad', 8, 2)->nullable()->comment('Radiación solar');
            $table->decimal('solradhi', 8, 2)->nullable()->comment('Radiación solar máxima');
            $table->decimal('solevo', 8, 2)->nullable()->comment('Evapotranspiración solar');

            // Temperatura del suelo (4 sensores)
            $table->decimal('soiltemp1', 8, 2)->nullable()->comment('Temperatura del suelo sensor 1');
            $table->decimal('soiltemp2', 8, 2)->nullable()->comment('Temperatura del suelo sensor 2');
            $table->decimal('soiltemp3', 8, 2)->nullable()->comment('Temperatura del suelo sensor 3');
            $table->decimal('soiltemp4', 8, 2)->nullable()->comment('Temperatura del suelo sensor 4');

            // Humedad del suelo (4 sensores)
            $table->decimal('soilhum1', 8, 2)->nullable()->comment('Humedad del suelo sensor 1');
            $table->decimal('soilhum2', 8, 2)->nullable()->comment('Humedad del suelo sensor 2');
            $table->decimal('soilhum3', 8, 2)->nullable()->comment('Humedad del suelo sensor 3');
            $table->decimal('soilhum4', 8, 2)->nullable()->comment('Humedad del suelo sensor 4');

            // Temperatura de hojas (4 sensores)
            $table->decimal('leaftemp1', 8, 2)->nullable()->comment('Temperatura de hoja sensor 1');
            $table->decimal('leaftemp2', 8, 2)->nullable()->comment('Temperatura de hoja sensor 2');
            $table->decimal('leaftemp3', 8, 2)->nullable()->comment('Temperatura de hoja sensor 3');
            $table->decimal('leaftemp4', 8, 2)->nullable()->comment('Temperatura de hoja sensor 4');

            // Humedad de hojas (4 sensores)
            $table->decimal('leafhum1', 8, 2)->nullable()->comment('Humedad de hoja sensor 1');
            $table->decimal('leafhum2', 8, 2)->nullable()->comment('Humedad de hoja sensor 2');
            $table->decimal('leafhum3', 8, 2)->nullable()->comment('Humedad de hoja sensor 3');
            $table->decimal('leafhum4', 8, 2)->nullable()->comment('Humedad de hoja sensor 4');

            // Calidad del aire (partículas y gas)
            $table->decimal('pm1', 8, 2)->nullable()->comment('Partículas PM1');
            $table->decimal('pm2_5', 8, 2)->nullable()->comment('Partículas PM2.5');
            $table->decimal('pm10', 8, 2)->nullable()->comment('Partículas PM10');
            $table->decimal('gas_ppm', 8, 2)->nullable()->comment('Gas (ppm)');

            // Estado y timestamps
            $table->tinyInteger('state')->default(1)->comment('Estado: 1=Activo, 0=Inactivo');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Índices
            $table->primary(['station_id', 'receipt_date'], 'pk_senva_data_station_receipt');
            $table->index('station_id', 'idx_senva_data_station_id');
            $table->index('receipt_date', 'idx_senva_data_receipt_date');
            $table->index(['station_id', 'registration_date'], 'idx_senva_data_station_reg');

            // Comentario de tabla
            $table->comment('Tabla de datos meteorológicos capturados por las estaciones');
        });

        Log::info("✅ [prepare-next-year-db] Tabla 'senva_data' creada en '{$dbName}'.");
        $this->info("✅ Tabla 'senva_data' creada en '{$dbName}'.");
    }
}
