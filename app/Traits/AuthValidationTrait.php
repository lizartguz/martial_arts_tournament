<?php
namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\UsersSubscriptionM;
use App\Models\UserDependencyM;

trait AuthValidationTrait
{
    public function validarUsuario($user, array $rolesNoPermitidos = ['marketing'], $forPay=null){
        
        if (!$user) {
            Auth::guard('web')->logout();
            return redirect()->route('login')->send();
        }
        if ($user->state !== 1) {
            Auth::guard('web')->logout();
            return redirect()->route('login');
        }
        if ($user->access_type == 0) {
           
            if ($user->hasAnyRole($rolesNoPermitidos)) {
                return redirect()->route('dashboard');
            }
        } else {
           
            $records = UserDependencyM::with(['user', 'subscription'])
                ->where('dependent_user_id', $user->id)
                ->get();            
            // Primero encontramos la fecha de expiración más reciente
            $mostRecentExpiry = $records
                ->sortByDesc(function ($item) {
                    return $item->subscription?->date_expiry;
                })
                ->first();

            // Buscamos si existe algún registro con extension temporal más reciente que esa fecha
            $data = $records
                ->filter(function ($item) use ($mostRecentExpiry) {
                    $tempExtension = $item->subscription?->temporary_extension_end;
                    return $tempExtension !== null && Carbon::parse($tempExtension)->greaterThanOrEqualTo($mostRecentExpiry->subscription->date_expiry);
                })
                ->sortByDesc(function ($item) {
                    return $item->subscription?->temporary_extension_end;
                })
                ->first();

            
            // Si encontramos un registro válido por extensión temporal, lo usamos. Si no, usamos el de expiración.
            if (!$data) {
                $data = $mostRecentExpiry;
            }            
            if (!$data) {             
                Auth::guard('web')->logout();
                return redirect()->route('login');
            }
            $currentDate = Carbon::now();
            $temporary_extension_endV = $data->subscription->temporary_extension_end;
            $date_expiryV = $data->subscription->date_expiry;            
            if (!is_null($temporary_extension_endV)) {
                if (Carbon::parse($temporary_extension_endV)->lessThan($currentDate)) {
                    if(!$forPay || is_null($forPay)){//exclusivo para pagos
                        Auth::guard('web')->logout();
                        return redirect()->route('login'); //->send();
                    }
                }                
            } elseif (!is_null($date_expiryV)) {               
                if (Carbon::parse($date_expiryV)->lessThan($currentDate)) {
                    if(!$forPay || is_null($forPay)){//exclusivo para pagos                        
                        Auth::guard('web')->logout();                        
                        return redirect()->route('login');
                    }
                }
            }else{
                if (is_null($date_expiryV)) {
                    Auth::guard('web')->logout();
                    return redirect()->route('login');
                }
            }
        }
    }
}