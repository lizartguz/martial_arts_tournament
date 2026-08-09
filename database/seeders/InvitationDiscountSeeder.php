<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InvitationDiscountM;

class InvitationDiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        InvitationDiscountM::create([
            'percentage_value' => 3.00,
            'state' => 1,
            'subscription_id' => 1,
        ]);

        InvitationDiscountM::create([
            'percentage_value' => 2.00,
            'state' => 1,
            'subscription_id' => 2,
        ]);
    }
}
