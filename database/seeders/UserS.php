<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UsersSubscriptionM;
use App\Models\UserDependencyM;
use App\Models\SubscriptionM;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserS extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ============= SUPER MANAGER (1 usuario) =============
        $user = User::updateOrCreate(
            ['email' => 'programacion14@senvatec.com'],
            [
                'username'     => 'superadministrador',
                'name'         => 'Super Administrador',
                'password'     => Hash::make('programacion14@senvatec.com'),
                'image'        => 'user_perfil.jpg',
                'access_type'  => 0, //interno
                'user_type'    => 1,
                'own_state'    => 1,
                'state'        => 1,
                'payment_state' => 1,
                'user_dependency_id' => null,
            ]
        );
        $user->syncRoles(['super_manager']);

        $userSubscription = UsersSubscriptionM::updateOrCreate(
            ['user_id' => $user->id, 'subscription_id' => 1],
            [
                'business_name' => null,
                'nit' => null,
                'reg_date' => date('Y-m-d'),
                'accumulation_date' => date('Y-m-d'),
                'start_date' => date('Y-m-d'),
                'expiration_notice' => date('Y-m-d', strtotime('+1 year')),
                'date_expiry' => '2070-12-31',
                'added_user_count' => 0,
                'added_user_total_cost' => 0,
                'amount' => 1,
                'is_discount' => false,
                'cost' => 0,
                'latitude' => '-17.788148',
                'longitude' => '-63.188735',
                'invitation_reference_data' => null,                
                'data_save' => SubscriptionM::where('id',1)->first(),
                'is_it_paid_user' => 1,
                'payment_type_user' => 4,
                'is_it_paid_renewal' => 1,
                'payment_type_renewal' => 4,
                'state' => 1,
                'station_id' => null,
            ]
        );

        UserDependencyM::updateOrCreate(
            ['dependent_user_id' => $user->id, 'user_subscription_id' => $userSubscription->id]
        );

        // ============= MANAGERS (2 usuarios) =============
        $managers = [
            [
                'username'=> 'robertosolis',
                'name'=> 'Roberto',
                'lastname'=> 'Solis',
                'email'=> 'roberto.solis@senvatec.com',
                'password'=> Hash::make('roberto.solis@senvatec.com'),
            ],
            [
                'username'=> 'patriciavargas',
                'name'=> 'Patricia',
                'lastname'=> 'Vargas',
                'email'=> 'patricia.vargas@senvatec.com',
                'password'=> Hash::make('patricia.vargas@senvatec.com'),
            ]
        ];

        foreach ($managers as $managerData) {
            $user = User::updateOrCreate(
                ['email' => $managerData['email']],
                array_merge($managerData, [
                    'image'=> 'user_perfil.jpg',  
                    'access_type'=> 0, //interno
                    'user_type'=> 1,
                    'own_state'=> 1,
                    'state'=> 1,
                    'payment_state' => 1,
                    'user_dependency_id' => null,
                ])
            );
            $user->syncRoles(['manager']);

            $userSubscription = UsersSubscriptionM::updateOrCreate(
                ['user_id' => $user->id, 'subscription_id' => 1],
                [
                    'business_name' => null,
                    'nit' => null,
                    'reg_date' => date('Y-m-d'),
                    'accumulation_date' => date('Y-m-d'),
                    'start_date' => date('Y-m-d'),
                    'expiration_notice' => date('Y-m-d', strtotime('+1 year')),
                    'date_expiry' => '2070-12-31',
                    'added_user_count' => 0,
                    'added_user_total_cost' => 0,
                    'amount' => 1,
                    'is_discount' => false,
                    'cost' => 0,
                    'latitude' => '-17.788148',
                    'longitude' => '-63.188735',
                    'invitation_reference_data' => null,
                    'data_save' => SubscriptionM::where('id', 1)->first(),
                    'is_it_paid_user' => 1,
                    'payment_type_user' => 4,
                    'is_it_paid_renewal' => 1,
                    'payment_type_renewal' => 4,
                    'state' => 1,
                    'station_id' => null,
                ]
            );

            UserDependencyM::updateOrCreate(
                ['dependent_user_id' => $user->id, 'user_subscription_id' => $userSubscription->id]
            );
        }

        // ============= SALES (2 usuarios) =============
        $sales = [
            [
                'username'=> 'fernandovargas',
                'name'=> 'Fernando',
                'lastname'=> 'Vargas',
                'email'=> 'fernando.vargas@senvatec.com',
                'password'=> Hash::make('fernando.vargas@senvatec.com'),
            ],
            [
                'username'=> 'danielamendoza',
                'name'=> 'Daniela',
                'lastname'=> 'Mendoza',
                'email'=> 'daniela.mendoza@senvatec.com',
                'password'=> Hash::make('daniela.mendoza@senvatec.com'),
            ]
        ];

        foreach ($sales as $salesData) {
            $user = User::updateOrCreate(
                ['email' => $salesData['email']],
                array_merge($salesData, [
                    'image'=> 'user_perfil.jpg',  
                    'access_type'=> 0, //interno
                    'user_type'=> 1,
                    'own_state'=> 1,
                    'state'=> 1,
                    'payment_state' => 1,
                    'user_dependency_id' => null,
                ])
            );
            $user->syncRoles(['sales']);

            $userSubscription = UsersSubscriptionM::updateOrCreate(
                ['user_id' => $user->id, 'subscription_id' => 1],
                [
                    'business_name' => null,
                    'nit' => null,
                    'reg_date' => date('Y-m-d'),
                    'accumulation_date' => date('Y-m-d'),
                    'start_date' => date('Y-m-d'),
                    'expiration_notice' => date('Y-m-d', strtotime('+1 year')),
                    'date_expiry' => '2070-12-31',
                    'added_user_count' => 0,
                    'added_user_total_cost' => 0,
                    'amount' => 1,
                    'is_discount' => false,
                    'cost' => 0,
                    'latitude' => '-17.788148',
                    'longitude' => '-63.188735',
                    'invitation_reference_data' => null,
                    'data_save' => SubscriptionM::where('id', 1)->first(),
                    'is_it_paid_user' => 1,
                    'payment_type_user' => 4,
                    'is_it_paid_renewal' => 1,
                    'payment_type_renewal' => 4,
                    'state' => 1,
                    'station_id' => null,
                ]
            );

            UserDependencyM::updateOrCreate(
                ['dependent_user_id' => $user->id, 'user_subscription_id' => $userSubscription->id]
            );
        }

        // ============= MARKETING (2 usuarios) =============
        $marketing = [
            [
                'username'=> 'gabrielatorres',
                'name'=> 'Gabriela',
                'lastname'=> 'Torres',
                'email'=> 'gabriela.torres@senvatec.com',
                'password'=> Hash::make('gabriela.torres@senvatec.com'),
            ],
            [
                'username'=> 'andresflores',
                'name'=> 'Andres',
                'lastname'=> 'Flores',
                'email'=> 'andres.flores@senvatec.com',
                'password'=> Hash::make('andres.flores@senvatec.com'),
            ]
        ];

        foreach ($marketing as $marketingData) {
            $user = User::updateOrCreate(
                ['email' => $marketingData['email']],
                array_merge($marketingData, [
                    'image'=> 'user_perfil.jpg',  
                    'access_type'=> 0, //interno
                    'user_type'=> 1,
                    'own_state'=> 1,
                    'state'=> 1,
                    'payment_state' => 1,
                    'user_dependency_id' => null,
                ])
            );
            $user->syncRoles(['marketing']);

            $userSubscription = UsersSubscriptionM::updateOrCreate(
                ['user_id' => $user->id, 'subscription_id' => 1],
                [
                    'business_name' => null,
                    'nit' => null,
                    'reg_date' => date('Y-m-d'),
                    'accumulation_date' => date('Y-m-d'),
                    'start_date' => date('Y-m-d'),
                    'expiration_notice' => date('Y-m-d', strtotime('+1 year')),
                    'date_expiry' => '2070-12-31',
                    'added_user_count' => 0,
                    'added_user_total_cost' => 0,
                    'amount' => 1,
                    'is_discount' => false,
                    'cost' => 0,
                    'latitude' => '-17.788148',
                    'longitude' => '-63.188735',
                    'invitation_reference_data' => null,
                    'data_save' => SubscriptionM::where('id', 1)->first(),
                    'is_it_paid_user' => 1,
                    'payment_type_user' => 4,
                    'is_it_paid_renewal' => 1,
                    'payment_type_renewal' => 4,
                    'state' => 1,
                    'station_id' => null,
                ]
            );

            UserDependencyM::updateOrCreate(
                ['dependent_user_id' => $user->id, 'user_subscription_id' => $userSubscription->id]
            );
        }

        // ============= METEOROLOGY (2 usuarios) =============
        $meteorology = [
            [
                'username'=> 'claudiachavez',
                'name'=> 'Claudia',
                'lastname'=> 'Chavez',
                'email'=> 'claudia.chavez@senvatec.com',
                'password'=> Hash::make('claudia.chavez@senvatec.com'),
            ],
            [
                'username'=> 'marcosrivera',
                'name'=> 'Marcos',
                'lastname'=> 'Rivera',
                'email'=> 'marcos.rivera@senvatec.com',
                'password'=> Hash::make('marcos.rivera@senvatec.com'),
            ]
        ];

        foreach ($meteorology as $meteorologyData) {
            $user = User::updateOrCreate(
                ['email' => $meteorologyData['email']],
                array_merge($meteorologyData, [
                    'image'=> 'user_perfil.jpg',  
                    'access_type'=> 0, //interno
                    'user_type'=> 1,
                    'own_state'=> 1,
                    'state'=> 1,
                    'payment_state' => 1,
                    'user_dependency_id' => null,
                ])
            );
            $user->syncRoles(['meteorology']);

            $userSubscription = UsersSubscriptionM::updateOrCreate(
                ['user_id' => $user->id, 'subscription_id' => 1],
                [
                    'business_name' => null,
                    'nit' => null,
                    'reg_date' => date('Y-m-d'),
                    'accumulation_date' => date('Y-m-d'),
                    'start_date' => date('Y-m-d'),
                    'expiration_notice' => date('Y-m-d', strtotime('+1 year')),
                    'date_expiry' => '2070-12-31',
                    'added_user_count' => 0,
                    'added_user_total_cost' => 0,
                    'amount' => 1,
                    'is_discount' => false,
                    'cost' => 0,
                    'latitude' => '-17.788148',
                    'longitude' => '-63.188735',
                    'invitation_reference_data' => null,
                    'data_save' => SubscriptionM::where('id', 1)->first(),
                    'is_it_paid_user' => 1,
                    'payment_type_user' => 4,
                    'is_it_paid_renewal' => 1,
                    'payment_type_renewal' => 4,
                    'state' => 1,
                    'station_id' => null,
                ]
            );

            UserDependencyM::updateOrCreate(
                ['dependent_user_id' => $user->id, 'user_subscription_id' => $userSubscription->id]
            );
        }

        // ============= METEOROLOGIST (2 usuarios) =============
        $meteorologists = [
            [
                'username'=> 'luisherrera',
                'name'=> 'Luis',
                'lastname'=> 'Herrera',
                'email'=> 'luis.herrera@senvatec.com',
                'password'=> Hash::make('luis.herrera@senvatec.com'),
            ],
            [
                'username'=> 'valeriagomez',
                'name'=> 'Valeria',
                'lastname'=> 'Gomez',
                'email'=> 'valeria.gomez@senvatec.com',
                'password'=> Hash::make('valeria.gomez@senvatec.com'),
            ]
        ];

        foreach ($meteorologists as $meteorologistData) {
            $user = User::updateOrCreate(
                ['email' => $meteorologistData['email']],
                array_merge($meteorologistData, [
                    'image'=> 'user_perfil.jpg',  
                    'access_type'=> 0, //interno
                    'user_type'=> 1,
                    'own_state'=> 1,
                    'state'=> 1,
                    'payment_state' => 1,
                    'user_dependency_id' => null,
                ])
            );
            $user->syncRoles(['meteorologist']);

            $userSubscription = UsersSubscriptionM::updateOrCreate(
                ['user_id' => $user->id, 'subscription_id' => 1],
                [
                    'business_name' => null,
                    'nit' => null,
                    'reg_date' => date('Y-m-d'),
                    'accumulation_date' => date('Y-m-d'),
                    'start_date' => date('Y-m-d'),
                    'expiration_notice' => date('Y-m-d', strtotime('+1 year')),
                    'date_expiry' => '2070-12-31',
                    'added_user_count' => 0,
                    'added_user_total_cost' => 0,
                    'amount' => 1,
                    'is_discount' => false,
                    'cost' => 0,
                    'latitude' => '-17.788148',
                    'longitude' => '-63.188735',
                    'invitation_reference_data' => null,
                    'data_save' => SubscriptionM::where('id', 1)->first(),
                    'is_it_paid_user' => 1,
                    'payment_type_user' => 4,
                    'is_it_paid_renewal' => 1,
                    'payment_type_renewal' => 4,
                    'state' => 1,
                    'station_id' => null,
                ]
            );

            UserDependencyM::updateOrCreate(
                ['dependent_user_id' => $user->id, 'user_subscription_id' => $userSubscription->id]
            );
        }

        // ============= TECHNICAL (2 usuarios) =============
        $technical = [
            [
                'username'=> 'pablocastillo',
                'name'=> 'Pablo',
                'lastname'=> 'Castillo',
                'email'=> 'pablo.castillo@senvatec.com',
                'password'=> Hash::make('pablo.castillo@senvatec.com'),
            ],
            [
                'username'=> 'monicaortiz',
                'name'=> 'Monica',
                'lastname'=> 'Ortiz',
                'email'=> 'monica.ortiz@senvatec.com',
                'password'=> Hash::make('monica.ortiz@senvatec.com'),
            ]
        ];

        foreach ($technical as $technicalData) {
            $user = User::updateOrCreate(
                ['email' => $technicalData['email']],
                array_merge($technicalData, [
                    'image'=> 'user_perfil.jpg',  
                    'access_type'=> 0, //interno
                    'user_type'=> 1,
                    'own_state'=> 1,
                    'state'=> 1,
                    'payment_state' => 1,
                    'user_dependency_id' => null,
                ])
            );
            $user->syncRoles(['technical']);

            $userSubscription = UsersSubscriptionM::updateOrCreate(
                ['user_id' => $user->id, 'subscription_id' => 1],
                [
                    'business_name' => null,
                    'nit' => null,
                    'reg_date' => date('Y-m-d'),
                    'accumulation_date' => date('Y-m-d'),
                    'start_date' => date('Y-m-d'),
                    'expiration_notice' => date('Y-m-d', strtotime('+1 year')),
                    'date_expiry' => '2070-12-31',
                    'added_user_count' => 0,
                    'added_user_total_cost' => 0,
                    'amount' => 1,
                    'is_discount' => false,
                    'cost' => 0,
                    'latitude' => '-17.788148',
                    'longitude' => '-63.188735',
                    'invitation_reference_data' => null,
                    'data_save' => SubscriptionM::where('id', 1)->first(),
                    'is_it_paid_user' => 1,
                    'payment_type_user' => 4,
                    'is_it_paid_renewal' => 1,
                    'payment_type_renewal' => 4,
                    'state' => 1,
                    'station_id' => null,
                ]
            );

            UserDependencyM::updateOrCreate(
                ['dependent_user_id' => $user->id, 'user_subscription_id' => $userSubscription->id]
            );
        }

        // ============= SUBSCRIBERS (30 usuarios - 1 por estación) =============
        
        // Datos de las 30 estaciones (mismas del StationsCompleteSeeder)
        $stationsData = [
            ['name' => 'Santa Cruz Centro', 'lat' => '-17.783333', 'lon' => '-63.182222', 'company' => 'Corporación Boliviana de Agroindustria', 'nit' => '1234567890'],
            ['name' => 'La Paz Plaza Murillo', 'lat' => '-16.500000', 'lon' => '-68.150000', 'company' => 'Grupo Financiero del Altiplano S.A.', 'nit' => '2345678901'],
            ['name' => 'Cochabamba Centro', 'lat' => '-17.393333', 'lon' => '-66.157222', 'company' => 'Industrias Alimentarias del Valle', 'nit' => '3456789012'],
            ['name' => 'Tarija San Lorenzo', 'lat' => '-21.412778', 'lon' => '-64.745556', 'company' => 'Viñedos y Bodegas Tarijeñas S.R.L.', 'nit' => '4567890123'],
            ['name' => 'Sucre Histórico', 'lat' => '-19.033333', 'lon' => '-65.266667', 'company' => 'Turismo y Patrimonio Chuquisaca', 'nit' => '5678901234'],
            ['name' => 'Potosí Minero', 'lat' => '-19.583333', 'lon' => '-65.750000', 'company' => 'Minera del Cerro Rico S.A.', 'nit' => '6789012345'],
            ['name' => 'Oruro Altiplano', 'lat' => '-17.966667', 'lon' => '-67.116667', 'company' => 'Cooperativa Minera Oruro Ltda.', 'nit' => '7890123456'],
            ['name' => 'Beni Trinidad', 'lat' => '-14.833333', 'lon' => '-64.900000', 'company' => 'Agroganadería Amazónica del Beni', 'nit' => '8901234567'],
            ['name' => 'Pando Cobija', 'lat' => '-11.027778', 'lon' => '-68.769444', 'company' => 'Castañeros de la Amazonía S.A.', 'nit' => '9012345678'],
            ['name' => 'El Alto Aeropuerto', 'lat' => '-16.505556', 'lon' => '-68.192222', 'company' => 'Logística y Transporte Aeroportuario', 'nit' => '1023456789'],
            ['name' => 'Montero Agrícola', 'lat' => '-17.337222', 'lon' => '-63.250556', 'company' => 'Agroindustrial Montero S.A.', 'nit' => '1134567890'],
            ['name' => 'Warnes Industrial', 'lat' => '-17.516667', 'lon' => '-63.166667', 'company' => 'Zona Franca Industrial Warnes', 'nit' => '1245678901'],
            ['name' => 'Quillacollo Valle', 'lat' => '-17.393056', 'lon' => '-66.279167', 'company' => 'Productores Lecheros del Valle', 'nit' => '1356789012'],
            ['name' => 'Tiquipaya Rural', 'lat' => '-17.337500', 'lon' => '-66.216667', 'company' => 'Cooperativa Agrícola Tiquipaya', 'nit' => '1467890123'],
            ['name' => 'Sacaba Urbano', 'lat' => '-17.398056', 'lon' => '-66.038333', 'company' => 'Municipio Productivo de Sacaba', 'nit' => '1578901234'],
            ['name' => 'Yacuiba Frontera', 'lat' => '-22.016667', 'lon' => '-63.683333', 'company' => 'Comercio Internacional Yacuiba', 'nit' => '1689012345'],
            ['name' => 'Villamontes Chaco', 'lat' => '-21.266667', 'lon' => '-63.466667', 'company' => 'Hidrocarburos del Chaco Boliviano', 'nit' => '1790123456'],
            ['name' => 'Uyuni Salar', 'lat' => '-20.461111', 'lon' => '-66.825000', 'company' => 'Turismo Salar de Uyuni S.R.L.', 'nit' => '1801234567'],
            ['name' => 'Tupiza Histórico', 'lat' => '-21.450000', 'lon' => '-65.716667', 'company' => 'Minería Artesanal Tupiza', 'nit' => '1912345678'],
            ['name' => 'Camiri Petrolero', 'lat' => '-20.047222', 'lon' => '-63.520833', 'company' => 'Petróleo y Gas Camiri S.A.', 'nit' => '2023456789'],
            ['name' => 'Riberalta Amazónico', 'lat' => '-11.008056', 'lon' => '-66.063889', 'company' => 'Exportadora de Castaña Riberalta', 'nit' => '2134567890'],
            ['name' => 'Guayaramerín Puerto', 'lat' => '-10.821667', 'lon' => '-65.358333', 'company' => 'Naviera Fluvial del Norte', 'nit' => '2245678901'],
            ['name' => 'Rurrenabaque Turístico', 'lat' => '-14.438333', 'lon' => '-67.528889', 'company' => 'Ecoturismo Rurrenabaque', 'nit' => '2356789012'],
            ['name' => 'Copacabana Lago', 'lat' => '-16.166111', 'lon' => '-69.088611', 'company' => 'Turismo Lago Titicaca S.R.L.', 'nit' => '2467890123'],
            ['name' => 'Sorata Montaña', 'lat' => '-15.771944', 'lon' => '-68.650000', 'company' => 'Aventura y Montañismo Sorata', 'nit' => '2578901234'],
            ['name' => 'Coroico Yungas', 'lat' => '-16.188889', 'lon' => '-67.725000', 'company' => 'Café Orgánico de los Yungas', 'nit' => '2689012345'],
            ['name' => 'Samaipata Valle', 'lat' => '-18.179444', 'lon' => '-63.872778', 'company' => 'Viñedos Samaipata S.A.', 'nit' => '2790123456'],
            ['name' => 'Vallegrande Histórico', 'lat' => '-18.489167', 'lon' => '-64.109722', 'company' => 'Patrimonio Cultural Vallegrande', 'nit' => '2801234567'],
            ['name' => 'Chulumani Yungas', 'lat' => '-16.408333', 'lon' => '-67.526667', 'company' => 'Productores de Coca Chulumani', 'nit' => '2912345678'],
            ['name' => 'Ascención de Guarayos', 'lat' => '-15.721389', 'lon' => '-63.143611', 'company' => 'Forestal Guarayos S.R.L.', 'nit' => '3023456789'],
        ];

        // Nombres y apellidos bolivianos para variedad
        $firstNames = ['Juan', 'María', 'Carlos', 'Ana', 'Luis', 'Rosa', 'Pedro', 'Carmen', 'José', 'Elena', 
                       'Miguel', 'Patricia', 'Roberto', 'Laura', 'Fernando', 'Isabel', 'Ricardo', 'Gloria', 
                       'Eduardo', 'Silvia', 'Pablo', 'Mónica', 'Andrés', 'Beatriz', 'Jorge', 'Daniela', 
                       'Raúl', 'Gabriela', 'Sergio', 'Valeria'];
        
        $lastNames = ['Garcia', 'Rodriguez', 'Martinez', 'Lopez', 'Gonzalez', 'Perez', 'Sanchez', 'Ramirez', 
                      'Torres', 'Flores', 'Rivera', 'Gomez', 'Diaz', 'Cruz', 'Morales', 'Gutierrez', 
                      'Ortiz', 'Chavez', 'Castillo', 'Herrera', 'Vargas', 'Mendoza', 'Rojas', 'Medina', 
                      'Castro', 'Romero', 'Suarez', 'Alvarez', 'Jimenez', 'Navarro'];

        for ($i = 1; $i <= 30; $i++) {
            $stationData = $stationsData[$i - 1];
            $firstName = $firstNames[$i - 1];
            $lastName = $lastNames[$i - 1];
            $email = strtolower($firstName . '.' . $lastName . '@empresa' . $i . '.com');
            
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'username' => strtolower($firstName . $lastName . $i),
                    'name' => $firstName,
                    'lastname' => $lastName,
                    'ci' => rand(1000000, 9999999) . ' ' . ['SC', 'LP', 'CB', 'TJ', 'CH'][array_rand(['SC', 'LP', 'CB', 'TJ', 'CH'])],
                    'number_phone' => '+591 ' . rand(60000000, 79999999),
                    'password' => Hash::make('12345678'),
                    'pin' => null,
                    'image' => 'user_perfil.jpg',
                    'device_identifier' => null,
                    'access_type' => 1, // externo
                    'user_type' => 1,
                    'own_state' => 1,
                    'state' => 1,
                    'payment_state' => 1,
                    'user_dependency_id' => null,
                ]
            );
            $user->syncRoles(['subscriber']);

            // Crear suscripción corporativa vinculada a la estación
            $userSubscription = UsersSubscriptionM::updateOrCreate(
                ['user_id' => $user->id, 'subscription_id' => 5],
                [
                    'business_name' => $stationData['company'],
                    'nit' => $stationData['nit'],
                    'reg_date' => date('Y-m-d'),
                    'accumulation_date' => date('Y-m-d'),
                    'start_date' => date('Y-m-d'),
                    'expiration_notice' => date('Y-m-d', strtotime('+1 year')),
                    'date_expiry' => date('Y-m-d', strtotime('+1 year')),
                    'added_user_count' => 0,
                    'added_user_total_cost' => 0,
                    'amount' => 1,
                    'is_discount' => false,
                    'cost' => 190,
                    'latitude' => $stationData['lat'],
                    'longitude' => $stationData['lon'],
                    'invitation_reference_data' => null,
                    'data_save' => SubscriptionM::where('id', 5)->first(), // Corporativo
                    'is_it_paid_user' => 1,
                    'payment_type_user' => 2,
                    'is_it_paid_renewal' => 1,
                    'payment_type_renewal' => 2,
                    'state' => 1,
                    'station_id' => $i, // Vinculado a la estación correspondiente
                ]
            );

            UserDependencyM::updateOrCreate(
                ['dependent_user_id' => $user->id, 'user_subscription_id' => $userSubscription->id]
            );

            echo "Usuario subscriber {$i}/30 procesado: {$firstName} {$lastName} - {$stationData['company']}\n";
        }

        echo "\n✅ Seeder de usuarios completado:\n";
        echo "   - 1 Super Manager\n";
        echo "   - 2 Managers\n";
        echo "   - 2 Sales\n";
        echo "   - 2 Marketing\n";
        echo "   - 2 Meteorology\n";
        echo "   - 2 Meteorologist\n";
        echo "   - 2 Technical\n";
        echo "   - 30 Subscribers (vinculados a las 30 estaciones)\n";
        echo "   TOTAL: 43 usuarios\n";
    }
}
