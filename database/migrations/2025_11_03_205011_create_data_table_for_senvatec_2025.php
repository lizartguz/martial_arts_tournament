<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rango de años para crear las bases de datos
     * Modifica estos valores según tus necesidades
     */
    private $startYear = 2025;
    private $endYear = 2030;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Iterar sobre el rango de años
        for ($year = $this->startYear; $year <= $this->endYear; $year++) {
            $connection = 'senvatec_db_' . $year;
            
            // Verificar si la conexión existe en config/database.php
            if (!config("database.connections.{$connection}")) {
                echo "⚠️  Conexión '{$connection}' no encontrada en config/database.php - Saltando...\n";
                continue;
            }
            
            // Asegurarnos de que la base de datos exista antes de intentar conectarnos
            try {
                $dbName = config("database.connections.{$connection}.database");
                
                // Crear una conexión auxiliar sin base de datos para no fallar en el connect
                $serverConfig = config("database.connections.{$connection}");
                $serverConfig['database'] = null;
                config(["database.connections.server_{$connection}" => $serverConfig]);
                
                \DB::connection("server_{$connection}")->statement("CREATE DATABASE IF NOT EXISTS `{$dbName}`");
                
                \DB::connection($connection)->getPdo();
                
                // Eliminar la tabla si existe (para migrate:fresh)
                if (Schema::connection($connection)->hasTable('senva_data')) {
                    echo "🗑️  Eliminando tabla existente 'senva_data' de '{$connection}'...\n";
                    Schema::connection($connection)->dropIfExists('senva_data');
                }
                
                // Limpiar registros de migración anteriores para esta tabla (si la tabla migrations existe)
                if (Schema::connection($connection)->hasTable('migrations')) {
                    \DB::connection($connection)->table('migrations')
                        ->where('migration', 'like', '%create_data_table_for_senvatec%')
                        ->delete();
                }
                
                echo "✅ Creando tabla 'senva_data' en '{$connection}'...\n";
            } catch (\Exception $e) {
                echo "⚠️  No se pudo conectar a '{$connection}' - Saltando...\n";
                continue;
            }
            
            Schema::connection($connection)->create('senva_data', function (Blueprint $table) {
            // Clave primaria compuesta: station_id + receipt_date
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
        }
        
        echo "\n🎉 Migración completada para años {$this->startYear} - {$this->endYear}\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Iterar sobre el rango de años para eliminar las tablas
        for ($year = $this->startYear; $year <= $this->endYear; $year++) {
            $connection = 'senvatec_db_' . $year;
            
            // Verificar si la conexión existe
            if (!config("database.connections.{$connection}")) {
                echo "⚠️  Conexión '{$connection}' no encontrada - Saltando...\n";
                continue;
            }
            
            // Verificar si la conexión es válida
            try {
                \DB::connection($connection)->getPdo();
                echo "🗑️  Eliminando tabla 'senva_data' de '{$connection}'...\n";
                Schema::connection($connection)->dropIfExists('senva_data');
            } catch (\Exception $e) {
                echo "⚠️  No se pudo conectar a '{$connection}' - Saltando...\n";
                continue;
            }
        }
        
        echo "\n✅ Rollback completado para años {$this->startYear} - {$this->endYear}\n";
    }
};
