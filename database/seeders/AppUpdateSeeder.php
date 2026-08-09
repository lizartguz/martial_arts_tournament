<?php

namespace Database\Seeders;

use App\Models\AppUpdateM;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AppUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AppUpdateM::create([
            'update_message_android' => 'no',
            'update_message_ios' => 'no',
            'block_android' => 'no',
            'block_ios' => 'no',
            'state' => 1,
            'user_id' => 1,
        ]);
        
    }
}
