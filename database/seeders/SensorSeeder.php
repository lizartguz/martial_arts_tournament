<?php

namespace Database\Seeders;

use App\Models\SensorM;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SensorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SensorM::create([
            'name' => 'Temperature',
            'abbreviation' => 'TEMP',
            'state' => 1,
        ]);
        SensorM::create([
            'name' => 'Humidity',
            'abbreviation' => 'HUM',
            'state' => 1,
        ]);
        SensorM::create([
            'name' => 'Wind direction and speed',
            'abbreviation' => 'WIND',
            'state' => 1,
        ]);
        SensorM::create([
            'name' => 'Barometric pressure',
            'abbreviation' => 'BARO',
            'state' => 1,
        ]);
        SensorM::create([
            'name' => 'Solar radiation',
            'abbreviation' => 'RAD_SOL',
            'state' => 1,
        ]);
        SensorM::create([
            'name' => 'uv radiation',
            'abbreviation' => 'RAD_UV',
            'state' => 1,
        ]);
        SensorM::create([
            'name' => 'Soil temperature',
            'abbreviation' => 'SOIL_TEMP',
            'state' => 1,
        ]);
        SensorM::create([
            'name' => 'Soil humidity',
            'abbreviation' => 'SOIL_HUM',
            'state' => 1,
        ]);
        SensorM::create([
            'name' => 'Leaf humidity',
            'abbreviation' => 'LEAF_HUM',
            'state' => 1,
        ]);
        SensorM::create([
            'name' => 'Soil temperature and humidity',
            'abbreviation' => 'SOIL_TH',
            'state' => 1,
        ]);
        SensorM::create([
            'name' => 'Proviometer',
            'abbreviation' => 'PROV',
            'state' => 1,
        ]);
        SensorM::create([
            'name' => 'Leaf',
            'abbreviation' => 'LEAF',
            'state' => 1,
        ]);
        SensorM::create([
            'name' => 'Temperature, soil, humidity and salinity',
            'abbreviation' => 'TSHS',
            'state' => 1,
        ]);
        SensorM::create([
            'name' => 'PM10 PM2.5 PM1',
            'abbreviation' => 'PM',
            'state' => 1,
        ]);
    }
}
