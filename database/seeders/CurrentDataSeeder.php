<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CurrentDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Este seeder crea datos meteorológicos actuales para las 30 estaciones.
     * Los datos son generados de manera realista considerando:
     * - Ubicación geográfica (latitud, longitud)
     * - Elevación (altura sobre el nivel del mar)
     * - Clima típico de cada región de Bolivia
     */
    public function run(): void
    {
        // Datos climáticos base por región de Bolivia
        $regionClimate = [
            // Santa Cruz, Beni, Pando - Tropical/Subtropical
            'tropical' => [
                'temp_base' => 28, 'temp_range' => 8, 'humidity_base' => 75, 'humidity_range' => 20,
                'rain_prob' => 0.4, 'wind_base' => 8, 'uv_base' => 9, 'pm_base' => 35
            ],
            // La Paz, El Alto, Oruro, Potosí - Altiplano
            'altiplano' => [
                'temp_base' => 12, 'temp_range' => 12, 'humidity_base' => 45, 'humidity_range' => 25,
                'rain_prob' => 0.2, 'wind_base' => 15, 'uv_base' => 12, 'pm_base' => 25
            ],
            // Cochabamba, Tarija, Sucre - Valle
            'valle' => [
                'temp_base' => 20, 'temp_range' => 10, 'humidity_base' => 55, 'humidity_range' => 20,
                'rain_prob' => 0.3, 'wind_base' => 10, 'uv_base' => 10, 'pm_base' => 30
            ],
            // Yungas - Subtropical húmedo
            'yungas' => [
                'temp_base' => 22, 'temp_range' => 8, 'humidity_base' => 80, 'humidity_range' => 15,
                'rain_prob' => 0.6, 'wind_base' => 6, 'uv_base' => 8, 'pm_base' => 20
            ],
        ];

        // Clasificación de cada estación por región climática
        $stationRegions = [
            1 => 'tropical',    // Santa Cruz Centro
            2 => 'altiplano',   // La Paz Plaza Murillo
            3 => 'valle',       // Cochabamba Centro
            4 => 'valle',       // Tarija San Lorenzo
            5 => 'valle',       // Sucre Histórico
            6 => 'altiplano',   // Potosí Minero
            7 => 'altiplano',   // Oruro Altiplano
            8 => 'tropical',    // Beni Trinidad
            9 => 'tropical',    // Pando Cobija
            10 => 'altiplano',  // El Alto Aeropuerto
            11 => 'tropical',   // Montero Agrícola
            12 => 'tropical',   // Warnes Industrial
            13 => 'valle',      // Quillacollo Valle
            14 => 'valle',      // Tiquipaya Rural
            15 => 'valle',      // Sacaba Urbano
            16 => 'tropical',   // Yacuiba Frontera
            17 => 'tropical',   // Villamontes Chaco
            18 => 'altiplano',  // Uyuni Salar
            19 => 'valle',      // Tupiza Histórico
            20 => 'tropical',   // Camiri Petrolero
            21 => 'tropical',   // Riberalta Amazónico
            22 => 'tropical',   // Guayaramerín Puerto
            23 => 'tropical',   // Rurrenabaque Turístico
            24 => 'altiplano',  // Copacabana Lago
            25 => 'yungas',     // Sorata Montaña
            26 => 'yungas',     // Coroico Yungas
            27 => 'valle',      // Samaipata Valle
            28 => 'valle',      // Vallegrande Histórico
            29 => 'yungas',     // Chulumani Yungas
            30 => 'tropical',   // Ascención de Guarayos
        ];

        // Elevaciones de cada estación (afectan presión y temperatura)
        $elevations = [
            1 => 416, 2 => 3640, 3 => 2558, 4 => 1875, 5 => 2810,
            6 => 4090, 7 => 3706, 8 => 155, 9 => 280, 10 => 4071,
            11 => 300, 12 => 375, 13 => 2548, 14 => 2750, 15 => 2650,
            16 => 650, 17 => 400, 18 => 3665, 19 => 2950, 20 => 820,
            21 => 141, 22 => 130, 23 => 205, 24 => 3841, 25 => 2695,
            26 => 1750, 27 => 1650, 28 => 2030, 29 => 1750, 30 => 250,
        ];

        $now = Carbon::now();
        $windDirections = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];

        for ($stationId = 1; $stationId <= 30; $stationId++) {
            $region = $stationRegions[$stationId];
            $climate = $regionClimate[$region];
            $elevation = $elevations[$stationId];

            // Calcular temperatura base según elevación (disminuye ~0.65°C por cada 100m)
            $tempAdjustment = -($elevation / 100) * 0.65;
            $baseTemp = $climate['temp_base'] + $tempAdjustment;

            // Generar temperatura actual con variación
            $tempout = round($baseTemp + (rand(-100, 100) / 100 * $climate['temp_range']), 1);
            $tempoutmin = round($tempout - rand(3, 8), 1);
            $tempoutmax = round($tempout + rand(3, 10), 1);
            $tempin = round($tempout + rand(-2, 5), 1);

            // Generar humedad
            $humout = rand(
                max(20, $climate['humidity_base'] - $climate['humidity_range']),
                min(99, $climate['humidity_base'] + $climate['humidity_range'])
            );
            $humoutmin = $humout - rand(5, 15);
            $humoutmax = $humout + rand(5, 15);
            $humin = rand(40, 70);

            // Calcular punto de rocío (aproximación)
            $dewptout = round($tempout - ((100 - $humout) / 5), 1);
            $dewptin = round($tempin - ((100 - $humin) / 5), 1);

            // Calcular presión atmosférica según elevación
            // Presión al nivel del mar ~1013 mb, disminuye ~12 mb por cada 100m
            $press = round(1013 - ($elevation * 0.12), 1);
            $seapress = round(1013 + (rand(-50, 50) / 10), 1);

            // Índice de calor
            $heatindexout = $tempout > 27 ? round($tempout + ($humout / 10), 1) : $tempout;

            // Viento
            $windspeed = rand(0, $climate['wind_base'] * 2);
            $windspeedhi = $windspeed + rand(5, 15);
            $winddir = rand(0, 360);
            $winddirstr = $windDirections[array_rand($windDirections)];
            $windavg = round(($windspeed + $windspeedhi) / 2, 1);
            $windchill = $tempout < 10 ? round($tempout - ($windspeed / 5), 1) : $tempout;

            // Segundo sensor de viento (si aplica)
            $windspeed2 = rand(0, $climate['wind_base'] * 2);
            $windspeedhi2 = $windspeed2 + rand(5, 15);
            $winddir2 = rand(0, 360);
            $winddirstr2 = $windDirections[array_rand($windDirections)];

            // Lluvia
            $hasRain = (rand(1, 100) / 100) < $climate['rain_prob'];
            $rainrate = $hasRain ? round(rand(0, 50) / 10, 1) : 0;
            $raintotal = $hasRain ? round(rand(0, 200) / 10, 1) : 0;

            // UV y radiación solar
            $uvindex = rand(0, $climate['uv_base'] + 2);
            $solrad = rand(0, 1200);
            $solradhi = $solrad + rand(50, 200);
            $solevo = round(rand(0, 100) / 10, 1);

            // Sensores de suelo (temperatura)
            $soiltemp1 = round($tempout + rand(-5, 5), 1);
            $soiltemp2 = round($tempout + rand(-5, 5), 1);
            $soiltemp3 = round($tempout + rand(-5, 5), 1);
            $soiltemp4 = round($tempout + rand(-5, 5), 1);

            // Sensores de humedad del suelo (en centibares)
            $soilhum1 = rand(10, 100);
            $soilhum2 = rand(10, 100);
            $soilhum3 = rand(10, 100);
            $soilhum4 = rand(10, 100);

            // Sensores de temperatura de hoja
            $leaftemp1 = round($tempout + rand(-3, 3), 1);
            $leaftemp2 = round($tempout + rand(-3, 3), 1);
            $leaftemp3 = round($tempout + rand(-3, 3), 1);
            $leaftemp4 = round($tempout + rand(-3, 3), 1);

            // Sensores de humedad de hoja
            $leafhum1 = rand(0, 15);
            $leafhum2 = rand(0, 15);
            $leafhum3 = rand(0, 15);
            $leafhum4 = rand(0, 15);

            // Calidad del aire (PM y gas)
            $pm10 = round($climate['pm_base'] + rand(-15, 30), 1);
            $pm2_5 = round($pm10 * 0.6 + rand(-5, 10), 1);
            $pm1 = round($pm2_5 * 0.5 + rand(-3, 5), 1);
            $gas_ppm = round(rand(50, 500) / 10.0, 1);

            // Insertar datos actuales
            DB::table('current_data')->updateOrInsert(
                ['station_id' => $stationId],
                [
                    'receipt_date' => $now,
                    'registration_date' => $now,
                    
                    // Temperaturas
                    'tempin' => $tempin,
                    'tempout' => $tempout,
                    'tempoutmin' => $tempoutmin,
                    'tempoutmax' => $tempoutmax,
                    
                    // Humedad
                    'humin' => $humin,
                    'humout' => $humout,
                    'humoutmin' => max(0, $humoutmin),
                    'humoutmax' => min(100, $humoutmax),
                    
                    // Punto de rocío e índice de calor
                    'dewptout' => $dewptout,
                    'dewptin' => $dewptin,
                    'heatindexout' => $heatindexout,
                    
                    // Presión
                    'press' => $press,
                    'seapress' => $seapress,
                    
                    // Viento
                    'windchill' => $windchill,
                    'windavg' => $windavg,
                    'windspeed' => $windspeed,
                    'winddir' => $winddir,
                    'windspeedhi' => $windspeedhi,
                    'winddirhi' => rand(0, 360),
                    'winddirstr' => $winddirstr,
                    
                    // Viento sensor 2
                    'windspeed2' => $windspeed2,
                    'winddir2' => $winddir2,
                    'windspeedhi2' => $windspeedhi2,
                    'winddirhi2' => rand(0, 360),
                    'winddirstr2' => $winddirstr2,
                    
                    // Lluvia
                    'rainrate' => $rainrate,
                    'raintotal' => $raintotal,
                    
                    // UV y radiación solar
                    'uvindex' => $uvindex,
                    'solrad' => $solrad,
                    'solradhi' => $solradhi,
                    'solevo' => $solevo,
                    
                    // Temperatura del suelo
                    'soiltemp1' => $soiltemp1,
                    'soiltemp2' => $soiltemp2,
                    'soiltemp3' => $soiltemp3,
                    'soiltemp4' => $soiltemp4,
                    
                    // Humedad del suelo
                    'soilhum1' => $soilhum1,
                    'soilhum2' => $soilhum2,
                    'soilhum3' => $soilhum3,
                    'soilhum4' => $soilhum4,
                    
                    // Temperatura de hoja
                    'leaftemp1' => $leaftemp1,
                    'leaftemp2' => $leaftemp2,
                    'leaftemp3' => $leaftemp3,
                    'leaftemp4' => $leaftemp4,
                    
                    // Humedad de hoja
                    'leafhum1' => $leafhum1,
                    'leafhum2' => $leafhum2,
                    'leafhum3' => $leafhum3,
                    'leafhum4' => $leafhum4,
                    
                    // Calidad del aire
                    'pm10' => max(0, $pm10),
                    'pm2_5' => max(0, $pm2_5),
                    'pm1' => max(0, $pm1),
                    'gas_ppm' => max(0, $gas_ppm),
                    
                    'state' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Información de la estación para el log
            $stationNames = [
                1 => 'Santa Cruz Centro', 2 => 'La Paz Plaza Murillo', 3 => 'Cochabamba Centro',
                4 => 'Tarija San Lorenzo', 5 => 'Sucre Histórico', 6 => 'Potosí Minero',
                7 => 'Oruro Altiplano', 8 => 'Beni Trinidad', 9 => 'Pando Cobija',
                10 => 'El Alto Aeropuerto', 11 => 'Montero Agrícola', 12 => 'Warnes Industrial',
                13 => 'Quillacollo Valle', 14 => 'Tiquipaya Rural', 15 => 'Sacaba Urbano',
                16 => 'Yacuiba Frontera', 17 => 'Villamontes Chaco', 18 => 'Uyuni Salar',
                19 => 'Tupiza Histórico', 20 => 'Camiri Petrolero', 21 => 'Riberalta Amazónico',
                22 => 'Guayaramerín Puerto', 23 => 'Rurrenabaque Turístico', 24 => 'Copacabana Lago',
                25 => 'Sorata Montaña', 26 => 'Coroico Yungas', 27 => 'Samaipata Valle',
                28 => 'Vallegrande Histórico', 29 => 'Chulumani Yungas', 30 => 'Ascención de Guarayos',
            ];

            echo sprintf(
                "Estación %2d/30: %-30s | Temp: %5.1f°C | Hum: %3d%% | Presión: %7.1f mb | Región: %-10s\n",
                $stationId,
                $stationNames[$stationId],
                $tempout,
                $humout,
                $press,
                $region
            );
        }

        echo "\n✅ Seeder de datos actuales completado: 30 estaciones con datos meteorológicos.\n";
        echo "   - Temperaturas ajustadas por elevación\n";
        echo "   - Datos climáticos según región (Tropical, Altiplano, Valle, Yungas)\n";
        echo "   - Todos los sensores configurados\n";
        echo "   - Calidad del aire incluida\n";
    }

    /**
     * Calcula el punto de rocío usando la fórmula de Magnus
     */
    private function calculateDewPoint($temperature, $humidity)
    {
        $a = 17.27;
        $b = 237.7;
        
        $alpha = (($a * $temperature) / ($b + $temperature)) + log($humidity / 100);
        $dewPoint = ($b * $alpha) / ($a - $alpha);
        
        return round($dewPoint, 1);
    }

    /**
     * Calcula la presión atmosférica según la elevación
     */
    private function calculatePressure($elevation, $temperature = 15)
    {
        // Fórmula barométrica simplificada
        $seaLevelPressure = 1013.25; // mb
        $pressure = $seaLevelPressure * pow((1 - (0.0065 * $elevation) / (288.15)), 5.255);
        
        return round($pressure, 1);
    }
}

