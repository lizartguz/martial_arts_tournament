<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(SubscriptionSeeder::class);
        $this->call(StationsCompleteSeeder::class);  // PRIMERO: Crear estaciones (necesarias para subscribers)
        $this->call(StationCalibrationSeeder::class);  // Configuracion de calibraciones por estacion
        $this->call(UserS::class);  // SEGUNDO: Crear usuarios (subscribers se vinculan a estaciones)
        $this->call(UpdateStationsUserCreatorSeeder::class);  // TERCERO: Actualizar user_creator_id a 1
        // CUARTO: Datos frescos (trunca y re-puebla current_data + senvatec_db_<anio>.senva_data)
        $this->call(StationDataSeeder::class);
        // $this->call(CurrentDataSeeder::class);  // Reemplazado por StationDataSeeder
        // $this->call(SenvaDataSeeder::class);    // Reemplazado por StationDataSeeder
        $this->call(SensorSeeder::class);  // SEXTO: Agregar sensores
        //$this->call(RecommendationSeeder::class);       
        //$this->call(ClassTypeStationSeeder::class);
        //$this->call(RecommendationSeeder::class);
        //$this->call(CredentialSeeder::class);
        //$this->call(AppUpdateSeeder::class);
        $this->call(DepartmentS::class);
        $this->call(BrandsAndModelsSeeder::class);
    }
}
