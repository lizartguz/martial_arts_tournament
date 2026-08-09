<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StationCalibrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            [
                'station_id' => 1,
                'variable_name' => 'winddir',
                'calibration_key' => 'wind_direction_offset_180_v1',
                'calibration_params' => json_encode(['offset' => 180]),
                'is_active' => 1,
            ],
            [
                'station_id' => 1,
                'variable_name' => 'winddirhi',
                'calibration_key' => 'wind_direction_offset_180_v1',
                'calibration_params' => json_encode(['offset' => 180]),
                'is_active' => 1,
            ],
            [
                'station_id' => 1,
                'variable_name' => 'windavg',
                'calibration_key' => 'wind_speed_ms_to_kmh_v1',
                'calibration_params' => json_encode(['factor' => 3.6]),
                'is_active' => 1,
            ],
            [
                'station_id' => 1,
                'variable_name' => 'windspeed',
                'calibration_key' => 'wind_speed_ms_to_kmh_v1',
                'calibration_params' => json_encode(['factor' => 3.6]),
                'is_active' => 1,
            ],
            [
                'station_id' => 1,
                'variable_name' => 'windspeedhi',
                'calibration_key' => 'wind_speed_ms_to_kmh_v1',
                'calibration_params' => json_encode(['factor' => 3.6]),
                'is_active' => 1,
            ],
            [
                'station_id' => 1,
                'variable_name' => 'windspeed2',
                'calibration_key' => 'wind_speed_ms_to_kmh_v1',
                'calibration_params' => json_encode(['factor' => 3.6]),
                'is_active' => 1,
            ],
            [
                'station_id' => 1,
                'variable_name' => 'windspeedhi2',
                'calibration_key' => 'wind_speed_ms_to_kmh_v1',
                'calibration_params' => json_encode(['factor' => 3.6]),
                'is_active' => 1,
            ],
        ];

        foreach ($rows as $row) {
            DB::table('station_calibrations')->updateOrInsert(
                [
                    'station_id' => $row['station_id'],
                    'variable_name' => $row['variable_name'],
                ],
                [
                    'calibration_key' => $row['calibration_key'],
                    'calibration_params' => $row['calibration_params'],
                    'is_active' => $row['is_active'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
