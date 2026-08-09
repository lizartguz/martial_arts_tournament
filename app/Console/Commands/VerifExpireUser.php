<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class VerifExpireUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:verif-expire-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(){
        date_default_timezone_set('America/La_Paz'); 
        $currentDate = date('Y-m-d');
        //Parents
        DB::table('users')
        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        ->join('users_subscriptions', 'users.id', '=', 'users_subscriptions.user_id')
        ->where('model_has_roles.role_id', 8)
        ->where('users.access_type', 1)
        ->where('users.user_type', 1)
        ->where('users.own_state', 1)
        ->where('users.state', 1)
        ->where(function($query) use ($currentDate) {
            $query->where(function($q) use ($currentDate) {
                $q->whereNull('users_subscriptions.temporary_extension_start')
                ->whereNull('users_subscriptions.temporary_extension_end')
                ->where('users_subscriptions.date_expiry', '<', $currentDate);
            })
            ->orWhere(function($q) use ($currentDate) {
                $q->whereNotNull('users_subscriptions.temporary_extension_start')
                ->whereNotNull('users_subscriptions.temporary_extension_end')
                ->where('users_subscriptions.temporary_extension_end', '<', $currentDate);
            });
        })
        ->update(['users.own_state' => 0]);

        //Children
        DB::table('users as hijos')
        ->whereIn('hijos.user_dependency_id', function($query) {
            $query->select('id')
                ->from('users')
                ->where('user_type', 1)
                ->where('own_state', 0);
        })
        ->where('hijos.user_type', 2)
        ->where('hijos.own_state', 1)
        ->update(['hijos.own_state' => 0]); 

       
    }
}
