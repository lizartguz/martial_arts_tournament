<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Eliminar la función si ya existe (para migrate:fresh)
        DB::unprepared('DROP FUNCTION IF EXISTS ET_CALCULATION');
        
        DB::unprepared('CREATE FUNCTION ET_CALCULATION(
                station_id INT, 
                measurement_date DATE, 
                temperature DECIMAL(6,2),           -- Temperatura: -99.99 a 999.99°C
                temperature_max DECIMAL(6,2),       -- Temperatura máxima
                temperature_min DECIMAL(6,2),       -- Temperatura mínima
                humidity DECIMAL(5,2),              -- Humedad: 0.00 a 100.00%
                wind_speed DECIMAL(6,2),            -- Velocidad viento: 0.00 a 999.99 km/h
                pressure DECIMAL(7,2),              -- Presión: 0.00 a 9999.99 hPa
                solar_radiation DECIMAL(7,2)        -- Radiación solar: 0.00 a 9999.99 W/m²
            ) RETURNS DECIMAL(8,4)                  -- ET: 0.0000 a 9999.9999 mm
            READS SQL DATA
            BEGIN
                -- Resultado final: evapotranspiración calculada
                DECLARE evapotranspiration DECIMAL(8,4);
                DECLARE accumulated_et DECIMAL(8,4);
                
                -- Variables de cálculo intermedio FAO Penman-Monteith
                DECLARE weighting_factor DOUBLE;                -- Factor de ponderación
                DECLARE latent_heat_vaporization DOUBLE;        -- Calor latente de vaporización (λ)
                DECLARE saturation_vapor_pressure DOUBLE;       -- Presión de vapor de saturación (ea)
                DECLARE actual_vapor_pressure DOUBLE;           -- Presión de vapor actual (ed)
                DECLARE wind_function DOUBLE;                   -- Función del viento (f)
                DECLARE daily_radiation DOUBLE;                 -- Radiación diaria (Rd)
                DECLARE slope_vapor_pressure_curve DOUBLE;      -- Pendiente curva presión vapor (Δ)
                DECLARE psychrometric_constant DOUBLE;          -- Constante psicrométrica (γ)
                DECLARE mean_temperature DOUBLE;                -- Temperatura media
                DECLARE relative_humidity DOUBLE;               -- Humedad relativa
                DECLARE net_radiation DOUBLE;                   -- Radiación neta (Rn)
                DECLARE daytime_wind_function DOUBLE;           -- Función viento diurno
                DECLARE nighttime_wind_function DOUBLE;         -- Función viento nocturno
                DECLARE wind_speed_ms DOUBLE;                   -- Velocidad viento en m/s
                DECLARE pressure_kpa DOUBLE;                    -- Presión en kPa

                -- Validación y normalización de temperatura
                SET temperature = IFNULL(temperature, 0);
                IF (temperature = -9999 OR temperature IS NULL) THEN
                    SET temperature = 0;
                END IF;

                -- Validación de temperaturas máxima y mínima
                SET temperature_max = IFNULL(temperature_max, temperature);
                SET temperature_min = IFNULL(temperature_min, temperature);
                IF (temperature_max = -9999 OR temperature_max IS NULL) THEN
                    SET temperature_max = temperature;
                END IF;
                IF (temperature_min = -9999 OR temperature_min IS NULL) THEN
                    SET temperature_min = temperature;
                END IF;  

                -- Validación de humedad y viento
                SET humidity = IFNULL(humidity, 0);
                SET wind_speed = IFNULL(wind_speed, 0);
                IF (wind_speed = -9999 OR wind_speed IS NULL) THEN
                    SET wind_speed = 0;
                END IF;

                -- Validación de presión (usar presión estándar si no hay dato)
                SET pressure = IFNULL(pressure, 1013.25);
                IF (pressure = -9999 OR pressure IS NULL) THEN
                    SET pressure = 1013.25;
                END IF;

                -- Validación de radiación solar
                SET solar_radiation = IFNULL(solar_radiation, 0);
                IF (solar_radiation = -9999 OR solar_radiation IS NULL) THEN
                    SET solar_radiation = 0;
                END IF;

                -- Conversión de unidades
                SET pressure_kpa = pressure * 0.1;                              -- hPa a kPa
                SET wind_speed_ms = wind_speed / 3.6;                           -- km/h a m/s
                
                -- Función del viento según hora del día
                SET daytime_wind_function = (0.3 + 0.0576) * wind_speed_ms;
                SET nighttime_wind_function = (0.125 + 0.0439) * wind_speed_ms;
                
                -- Parámetros ambientales
                SET relative_humidity = humidity;
                SET mean_temperature = (temperature_max + temperature_min) / 2;
                
                -- Radiación neta
                SET net_radiation = ((0.77 - 0.00146) * mean_temperature) * solar_radiation;
                
                -- Calor latente de vaporización
                SET latent_heat_vaporization = 694.5 * (1 - 0.000946 * mean_temperature);
                
                -- Presión de vapor de saturación y actual
                SET saturation_vapor_pressure = 0.6108 * EXP(17.27 * temperature / (237.7 + temperature));
                SET actual_vapor_pressure = saturation_vapor_pressure * relative_humidity / 100;

                -- Selección de función del viento según radiación
                IF (net_radiation > 0) THEN 
                    SET wind_function = daytime_wind_function;
                ELSE
                    SET wind_function = nighttime_wind_function;
                END IF;

                -- Pendiente de la curva de presión de vapor
                SET slope_vapor_pressure_curve = 0.154793397;
                
                -- Constante psicrométrica
                SET psychrometric_constant = 0.000646 * (1 + 0.000946 * mean_temperature) * pressure_kpa;

                -- Obtener ET acumulada del día actual
                SET accumulated_et = IFNULL(
                    (SELECT cd.solevo 
                     FROM current_data cd
                     WHERE cd.station_id = station_id 
                       AND DATE(measurement_date) = DATE(cd.receipt_date) 
                     ORDER BY cd.receipt_date DESC 
                     LIMIT 1),
                    0
                );

                -- Validar ET acumulada
                IF (accumulated_et = -9999 OR accumulated_et IS NULL) THEN
                    SET accumulated_et = 0;
                END IF;

                -- Cálculo final de evapotranspiración (método FAO Penman-Monteith simplificado)
                SET latent_heat_vaporization = 694.5 * (1 - 0.000946 * mean_temperature);
                SET daily_radiation = (solar_radiation * 86400) / 1000000;
                SET weighting_factor = 1 - psychrometric_constant;
                
                -- Ecuación de evapotranspiración
                SET evapotranspiration = weighting_factor * daily_radiation / latent_heat_vaporization 
                                       + (1 - weighting_factor) * (saturation_vapor_pressure - actual_vapor_pressure) * wind_function;

                -- Retornar ET acumulada más la ET incremental (dividida por 3 para ajuste temporal)
                RETURN IFNULL((accumulated_et + (evapotranspiration / 3)), NULL);
            END
        ');
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP FUNCTION IF EXISTS ET_CALCULATION');
    }
};
