<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateStationsUserCreatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Este seeder actualiza el campo user_creator_id a 1 (super_manager)
     * para todas las estaciones después de que se hayan creado los usuarios.
     * Debe ejecutarse después de StationsCompleteSeeder y UserS.
     */
    public function run(): void
    {
        // Verificar que existe el usuario con ID 1
        $userExists = DB::table('users')->where('id', 1)->exists();
        
        if (!$userExists) {
            echo "⚠️  ADVERTENCIA: El usuario con ID 1 no existe. No se puede actualizar user_creator_id.\n";
            echo "   Por favor, asegúrate de ejecutar UserS antes de este seeder.\n";
            return;
        }

        // Actualizar todas las estaciones con user_creator_id = 1
        $updated = DB::table('stations')
            ->whereNull('user_creator_id')
            ->update([
                'user_creator_id' => 1,
                'updated_at' => now(),
            ]);

        echo "✅ Seeder completado: {$updated} estación(es) actualizada(s) con user_creator_id = 1\n";
        echo "   (Super Manager asignado como creador de todas las estaciones)\n";
    }
}

