<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionM;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SubscriptionM::create([
            'name' => 'Personal Demo',
            'tag' => 'demo personal',
            'price' => 0,
            'duration' => 0,
            'duration_discount' => 12,
            'duration_value_discount' => 0,
            'discount_value_stations' => 0,
            'number_stations_discount' => 1,
            'amount' => 1,
            'state_de' => 0,
            'state_dd' => 0,
            'state' => 1,
        ]);

        SubscriptionM::create([
            'name' => 'Profesional Demo',
            'tag' => 'demo professional',
            'price' => 190,
            'duration' => 1,
            'duration_discount' => 0,
            'duration_value_discount' => 0,
            'discount_value_stations' => 0,
            'number_stations_discount' => 1,
            'amount' => 1,
            'state_de' => 0,
            'state_dd' => 0,
            'state' => 1,
        ]);
        
        SubscriptionM::create([
            'name' => 'Personal',
            'tag' => 'personal',
            'price' => 25,
            'duration' => 1,
            'duration_discount' => 2,
            'duration_value_discount' => 10,
            'discount_value_stations' => 0,            
            'number_stations_discount' => 0,
            'amount' => 1,
            'state_de' => 1,
            'state_dd' => 1,
            'state' => 1,
        ]);

        SubscriptionM::create([
            'name' => 'Profesional',
            'tag' => 'professional',
            'price' => 190,
            'duration' => 1,
            'duration_discount' => 0,
            'duration_value_discount' => 0,
            'discount_value_stations' => 0,
            'number_stations_discount' => 1,
            'amount' => 1,
            'state_de' => 0,
            'state_dd' => 0,
            'state' => 1,
        ]);

        SubscriptionM::create([
            'name' => 'Corporativo',
            'tag' => 'corporate',
            'price' => 190,
            'duration' => 1,
            'duration_discount' => 2,
            'duration_value_discount' => 10,
            'discount_value_stations' => 50,
            'number_stations_discount' => 1,
            'amount' => 10,
            'state_de' => 1,
            'state_dd' => 1,
            'state' => 1,
        ]);

        
    }
}
