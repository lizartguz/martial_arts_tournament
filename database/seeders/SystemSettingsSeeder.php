<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SystemSettingsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('system_settings')->updateOrInsert(
            ['id' => 1],
            [
                'product_name' => 'Combate Real',
                'public_title' => 'Combate Real',
                'logo_path' => 'images/logo_circle.png',
                'favicon_path' => 'frontend/images/logo_with_text.png',
                'default_image' => 'frontend/images/senvatec-cta-landscape.png',
                'contact_email' => 'contacto@combatereal.dev',
                'contact_phone' => '+59170000000',
                'whatsapp_phone' => '+59170000000',
                'social_links' => json_encode([
                    'facebook' => null,
                    'instagram' => null,
                    'youtube' => null,
                    'tiktok' => null,
                ]),
                'short_description' => 'Promotora de artes marciales mixtas con eventos, peleadores, suscripciones y contacto comercial.',
                'seo_title' => 'Combate Real',
                'seo_description' => 'Eventos de artes marciales mixtas, cartelera, peleadores y suscripciones.',
                'landing_show_rankings' => false,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $this->seedLocations();
        $this->seedWeightClasses();
        $this->seedRootUser();
    }

    private function seedLocations(): void
    {
        DB::table('countries')->updateOrInsert(
            ['iso2' => 'BO'],
            ['name' => 'Bolivia', 'iso3' => 'BOL', 'status' => 1, 'updated_at' => now(), 'created_at' => now()]
        );

        $countryId = DB::table('countries')->where('iso2', 'BO')->value('id');

        foreach (['Santa Cruz de la Sierra', 'La Paz', 'Cochabamba'] as $city) {
            DB::table('cities')->updateOrInsert(
                ['country_id' => $countryId, 'name' => $city],
                ['status' => 1, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    private function seedWeightClasses(): void
    {
        $classes = [
            ['Peso mosca masculino', 'male', 56.70, 125],
            ['Peso gallo masculino', 'male', 61.20, 135],
            ['Peso pluma masculino', 'male', 65.80, 145],
            ['Peso ligero masculino', 'male', 70.30, 155],
            ['Peso mosca femenino', 'female', 56.70, 125],
            ['Peso gallo femenino', 'female', 61.20, 135],
            ['Peso pluma femenino', 'female', 65.80, 145],
            ['Peso ligero femenino', 'female', 70.30, 155],
        ];

        foreach ($classes as $index => [$name, $gender, $maxKg, $maxLb]) {
            DB::table('weight_classes')->updateOrInsert(
                ['slug' => str($name)->slug()->toString()],
                [
                    'name' => $name,
                    'gender' => $gender,
                    'min_weight_kg' => null,
                    'max_weight_kg' => $maxKg,
                    'max_weight_lb' => $maxLb,
                    'display_order' => $index + 1,
                    'status' => 1,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    private function seedRootUser(): void
    {
        $user = User::query()->updateOrCreate(
            ['email' => 'sarteaga@root.dev'],
            [
                'username' => 'sarteaga-root',
                'name' => 'Santiago',
                'lastname' => 'Arteaga',
                'number_phone' => null,
                'password' => Hash::make('sarteaga@root.dev'),
                'state' => 1,
            ]
        );

        $user->assignRole('super_manager');
    }
}
