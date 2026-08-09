<?php

namespace Database\Seeders;

use App\Models\BrandM;
use App\Models\StationModelM;
use Illuminate\Database\Seeder;

class BrandsAndModelsSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            'Davis Instruments' => [
                'Vantage Pro 2',
                'Vantage Vue',
                'EnviroMonitor',
                'WeatherLink Live',
            ],
            'Campbell Scientific' => [
                'CR300',
                'CR1000X',
                'CR6',
                'CR800',
            ],
            'Vaisala' => [
                'WXT536',
                'WXT530',
                'WMT700',
                'AWS310',
            ],
            'Onset HOBO' => [
                'RX3000',
                'U30',
                'MX2301',
            ],
            'Lufft' => [
                'WS600',
                'WS800',
                'WS301',
            ],
            'ICT International' => [
                'SFM1',
                'HydraProbe',
            ],
            'Meter Group' => [
                'ATMOS 41',
                'Em50',
            ],
        ];

        foreach ($catalog as $brandName => $models) {
            $brand = BrandM::firstOrCreate(
                ['name' => $brandName],
                ['state' => 1]
            );

            foreach ($models as $modelName) {
                StationModelM::firstOrCreate(
                    ['brand_id' => $brand->id, 'name' => $modelName],
                    ['state' => 1]
                );
            }
        }
    }
}
