<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Ejecuta la carga de datos definida por el seeder.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            SystemSettingsSeeder::class,
        ]);

        if (! app()->environment('production')) {
            $this->call(MmaDemoSeeder::class);
        }
    }
}
