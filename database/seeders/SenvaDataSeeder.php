<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\StationM;
use Carbon\Carbon;

class SenvaDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Crea 10 registros por estación en senvatec_db_2025
     * Cada registro tiene 10 minutos de diferencia
     */
    public function run(): void
    {
        echo "🌦️  Iniciando seeder de datos históricos para senvatec_db_2025...\n\n";
        
        // Obtener todas las estaciones
        $stations = StationM::select('id', 'name', 'latitude', 'longitude')->get();
        
        if ($stations->isEmpty()) {
            echo "⚠️  No se encontraron estaciones. Ejecuta el seeder de estaciones primero.\n";
            return;
        }
        
        echo "📊 Total de estaciones: " . $stations->count() . "\n";
        echo "📝 Registros por estación: 10 (cada 10 minutos)\n\n";
        
        // Fecha y hora actual como punto de inicio
        $baseDateTime = Carbon::now();
        
        $totalRecords = 0;
        $connection = 'senvatec_db_2025';
        
        // Verificar que la conexión existe
        if (!config("database.connections.{$connection}")) {
            echo "❌ Error: La conexión '{$connection}' no está configurada.\n";
            return;
        }
        
        // Asegurar que la base de datos existe primero
        $dbName = config("database.connections.{$connection}.database");
        $serverConfig = config("database.connections.{$connection}");
        $serverConfig['database'] = null;
        config(["database.connections.server_{$connection}" => $serverConfig]);
        \DB::connection("server_{$connection}")->statement("CREATE DATABASE IF NOT EXISTS `{$dbName}`");
        
        DB::connection($connection)->beginTransaction();
        
        try {
            foreach ($stations as $index => $station) {
                echo "Estación " . ($index + 1) . "/" . $stations->count() . ": {$station->name} (ID: {$station->id})... ";
                
                // Crear 10 registros para esta estación
                for ($i = 0; $i < 10; $i++) {
                    // Calcular la fecha de recepción (retrocedemos en el tiempo)
                    // El registro más reciente es el actual, y vamos hacia atrás
                    $receiptDate = $baseDateTime->copy()->subMinutes($i * 10);
                    
                    // Generar datos meteorológicos ficticios pero realistas
                    $data = $this->generateRealisticWeatherData($station, $receiptDate);
                    
                    // Insertar el registro
                    DB::connection($connection)->table('senva_data')->insert($data);
                    $totalRecords++;
                }
                
                echo "✅ 10 registros creados\n";
            }
            
            DB::connection($connection)->commit();
            
            echo "\n🎉 Seeder completado exitosamente!\n";
            echo "📊 Total de registros creados: {$totalRecords}\n";
            echo "🗄️  Base de datos: {$connection}\n";
            echo "📅 Rango de fechas: " . $baseDateTime->copy()->subMinutes(90)->format('Y-m-d H:i:s') . " a " . $baseDateTime->format('Y-m-d H:i:s') . "\n";
            
        } catch (\Exception $e) {
            DB::connection($connection)->rollBack();
            echo "\n❌ Error: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Genera datos meteorológicos ficticios pero realistas
     * Basado en los rangos típicos para Bolivia
     */
    private function generateRealisticWeatherData($station, $receiptDate)
    {
        // Determinar región climática basada en latitud (simplificado)
        $latitude = floatval($station->latitude);
        
        // Ajustar rangos según la región
        if ($latitude < -20) {
            // Región Sur (más frío)
            $tempRange = [8, 25];
            $humRange = [30, 70];
            $rainChance = 0.15;
        } elseif ($latitude > -16) {
            // Región Norte/Tropical (más cálido y húmedo)
            $tempRange = [18, 32];
            $humRange = [50, 90];
            $rainChance = 0.35;
        } else {
            // Región Central
            $tempRange = [12, 28];
            $humRange = [40, 80];
            $rainChance = 0.25;
        }
        
        // Ajustar temperatura según hora del día
        $hour = $receiptDate->hour;
        $tempAdjustment = 0;
        if ($hour >= 12 && $hour <= 16) {
            // Horas más calientes del día
            $tempAdjustment = 5;
        } elseif ($hour >= 0 && $hour <= 6) {
            // Horas más frías de la noche/madrugada
            $tempAdjustment = -5;
        } elseif ($hour >= 7 && $hour <= 11) {
            // Mañana
            $tempAdjustment = 2;
        } else {
            // Tarde/Noche
            $tempAdjustment = -2;
        }
        
        // Generar valores base
        $tempout = round($this->randomFloat($tempRange[0] + $tempAdjustment, $tempRange[1] + $tempAdjustment), 2);
        $humout = round($this->randomFloat($humRange[0], $humRange[1]), 2);
        $press = round($this->randomFloat(1010, 1025), 2);
        
        // Temperatura min/max basadas en la temperatura actual
        $tempoutmin = round($tempout - $this->randomFloat(0.5, 2), 2);
        $tempoutmax = round($tempout + $this->randomFloat(0.5, 3), 2);
        
        // Humedad min/max (asegurar rango 0-100)
        $humoutmin = max(0, round($humout - $this->randomFloat(2, 10), 2));
        $humoutmax = min(100, round($humout + $this->randomFloat(2, 10), 2));
        
        // Punto de rocío (aproximación)
        $dewptout = round($tempout - ((100 - $humout) / 5), 2);
        
        // Viento (más viento durante el día)
        $windspeed = $hour >= 10 && $hour <= 18 
            ? round($this->randomFloat(5, 25), 2) 
            : round($this->randomFloat(1, 12), 2);
        $winddir = fmod(round($this->randomFloat(0, 359), 2) + 360, 360);
        $windspeedhi = round($windspeed + $this->randomFloat(3, 10), 2);
        
        // Sensor de viento 2 (con variación)
        $windspeed2 = max(0, round($windspeed + $this->randomFloat(-2, 2), 2));
        $winddir2 = round($winddir + $this->randomFloat(-30, 30), 2);
        // Normalizar dirección del viento al rango 0-359
        $winddir2 = fmod($winddir2 + 360, 360);
        $windspeedhi2 = round($windspeed2 + $this->randomFloat(3, 10), 2);
        
        // Precipitación (probabilística)
        $raintotal = (rand(0, 100) / 100) < $rainChance 
            ? round($this->randomFloat(0, 5), 2) 
            : 0;
        $rainrate = $raintotal > 0 
            ? round($this->randomFloat(0, $raintotal), 2) 
            : 0;
        
        // Radiación solar (solo durante el día)
        $solrad = $hour >= 6 && $hour <= 18 
            ? round($this->randomFloat(200, 1000), 2) 
            : 0;
        $solradhi = $solrad > 0 
            ? round($solrad + $this->randomFloat(50, 200), 2) 
            : 0;
        $uvindex = $hour >= 10 && $hour <= 16 
            ? round($this->randomFloat(5, 12), 2) 
            : round($this->randomFloat(0, 4), 2);
        
        // Sensores agrícolas (aleatorios)
        $soiltemp1 = round($this->randomFloat($tempout - 5, $tempout + 3), 2);
        $soilhum1 = round($this->randomFloat(20, 60), 2);
        $leaftemp1 = round($this->randomFloat($tempout - 3, $tempout + 2), 2);
        $leafhum1 = round($this->randomFloat(40, 90), 2);
        
        // Calidad del aire (aleatorio, algunas estaciones)
        $pm10 = (rand(0, 100) / 100) < 0.3 ? round($this->randomFloat(10, 50), 2) : null;
        $pm2_5 = $pm10 ? round($pm10 * 0.6, 2) : null;
        $pm1 = $pm2_5 ? round($pm2_5 * 0.5, 2) : null;
        $gas_ppm = (rand(0, 100) / 100) < 0.3 ? round($this->randomFloat(50, 500), 2) : null;
        
        $now = Carbon::now();
        
        return [
            'station_id' => $station->id,
            'receipt_date' => $receiptDate->format('Y-m-d H:i:s'),
            'registration_date' => $receiptDate->format('Y-m-d H:i:s'),
            
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
            
            // Punto de rocío
            'dewptout' => $dewptout,
            'dewptin' => round($dewptout + $this->randomFloat(-1, 1), 2),
            'heatindexout' => round($tempout + $this->randomFloat(0, 3), 2),
            
            // Presión
            'press' => $press,
            'seapress' => round($press + $this->randomFloat(1, 5), 2),
            
            // Viento sensor 1
            'windchill' => $tempout < 15 ? round($tempout - $this->randomFloat(1, 5), 2) : null,
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
            
            // Precipitación
            'rainrate' => $rainrate,
            'raintotal' => $raintotal,
            
            // Radiación solar
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
            
            // Sensores de hojas
            'leaftemp1' => $leaftemp1,
            'leaftemp2' => round($this->randomFloat($leaftemp1 - 2, $leaftemp1 + 2), 2),
            'leaftemp3' => round($this->randomFloat($leaftemp1 - 3, $leaftemp1 + 3), 2),
            'leaftemp4' => round($this->randomFloat($leaftemp1 - 2, $leaftemp1 + 2), 2),
            
            'leafhum1' => $leafhum1,
            'leafhum2' => max(0, min(100, round($this->randomFloat($leafhum1 - 10, $leafhum1 + 10), 2))),
            'leafhum3' => max(0, min(100, round($this->randomFloat($leafhum1 - 15, $leafhum1 + 15), 2))),
            'leafhum4' => max(0, min(100, round($this->randomFloat($leafhum1 - 10, $leafhum1 + 10), 2))),
            
            // Calidad del aire
            'pm1' => $pm1,
            'pm2_5' => $pm2_5,
            'pm10' => $pm10,
            'gas_ppm' => $gas_ppm,
            
            // Estado
            'state' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
    
    /**
     * Genera un número float aleatorio entre min y max
     */
    private function randomFloat($min, $max)
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }
    
    /**
     * Convierte dirección del viento en grados a texto
     */
    private function getWindDirection($degrees)
    {
        // Normalizar grados al rango 0-359
        $degrees = fmod($degrees + 360, 360);
        
        $directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSO', 'SO', 'OSO', 'O', 'ONO', 'NO', 'NNO'];
        $index = round($degrees / 22.5) % 16;
        
        // Asegurar que el índice esté en el rango válido
        $index = max(0, min(15, $index));
        
        return $directions[$index];
    }
}
