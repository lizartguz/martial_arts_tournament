<?php

namespace App\Http\Controllers\Api\v1;

use DateTime;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserDependencyM;
use App\Models\UsersSubscriptionM;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthApiController extends Controller
{
    private const MANAGER_ROLE_NAMES = [
        'super_manager',
        'manager',
        'meteorology',
        'meteorologist',
        'technical',
    ];

    public function loginApi(Request $request){
        try {
            $currentDate = date('Y-m-d');
            DB::beginTransaction();
            /*
            $user = User::join('users_subscriptions as us','us.user_id','users.id')
            ->join('subscriptions as s','s.id','us.subscription_id')
            ->where('email',$request->user)
            ->orWhere('number_phone',$request->user)
            ->select('users.id', 'users.name', 'users.lastname','users.password', 'users.email', 'users.number_phone', 'users.image', 'users.device_identifier','users.state','users.is_update','us.date_expiry','us.temporary_extension_end','us.temporary_extension_end','s.name as type_subscriptions')->first();
            */
            $user = User::leftjoin('users_subscriptions as us','us.user_id','users.id')
            ->leftjoin('subscriptions as s','s.id','us.subscription_id')
            ->where('email',$request->user)
            ->orWhere('number_phone',$request->user)
            ->select('users.id', 'users.name', 'users.lastname','users.password', 'users.email', 'users.number_phone', 'users.image', 'users.device_identifier','users.state','users.user_type','users.is_update','us.date_expiry','us.temporary_extension_end','us.temporary_extension_end','s.name as type_subscriptions','users.payment_state')->first();
            if($user!=null){
                if($user->user_type==2 || $user->user_type==3){
                    
                    $subscriptions = UserDependencyM::join('users_subscriptions as us', 'us.id', '=', 'user_dependency.user_subscription_id')
                        ->join('subscriptions as s','s.id','us.subscription_id')
                        ->where('user_dependency.dependent_user_id', $user->id)
                        ->select('us.date_expiry','us.temporary_extension_end','us.temporary_extension_end','s.name as type_subscriptions')
                        ->first();
                    
                    $user->date_expiry = $subscriptions->date_expiry;
                    $user->temporary_extension_end = $subscriptions->temporary_extension_end;
                    $user->temporary_extension_end = $subscriptions->temporary_extension_end;
                    $user->type_subscriptions = $subscriptions->type_subscriptions;
                }

            }
            if($user!=null){
                //if($user->hasRole('subscriber')){
                    $newIdentifier = null;
                    if(Hash::check($request->password, $user->password)) {
                        if($user->device_identifier == null){   
                            $newIdentifier =  $request->identifier.'_'.$user->id;                    
                            User::where('id',$user->id)
                            ->update(['device_identifier' => $newIdentifier]);
                            DB::commit();
                        }else{

                            if($user->is_update === 'yes'){ 
                                $newIdentifier =  $request->identifier.'_'.$user->id;                    
                                User::where('id',$user->id)
                                ->update(['device_identifier' => $newIdentifier,'is_update' => 'no']);
                                DB::commit();
                            }else{
                                //caso else: Updating the identifier only if the field is_update is null. If it is 'no', it cannot be updated..
                                if($user->is_update === null){//la primera vez para actualizar a un mejor identificador
                                    $position = strrpos($user->device_identifier, '_');
                                    $identifier_whithout_ID = substr($user->device_identifier, 0, $position);                                   
                                    $position2 = strrpos($identifier_whithout_ID, '◘');
                                    if($position2!=false){
                                        $identifier_whithout_ID2 = substr($user->device_identifier, 0, $position);
                                        if (str_contains($request->identifier, $identifier_whithout_ID2)) {
                                            $newIdentifier =  $request->identifier.'_'.$user->id;                    
                                            User::where('id',$user->id)
                                            ->update(['device_identifier' => $newIdentifier,'is_update' => 'no']);
                                            DB::commit();
                                        }
                                    }else{
                                        if (str_contains($request->identifier, $identifier_whithout_ID)) {
                                            $newIdentifier =  $request->identifier.'_'.$user->id;                    
                                            User::where('id',$user->id)
                                            ->update(['device_identifier' => $newIdentifier,'is_update' => 'no']);
                                            DB::commit();
                                        }
                                    }
                                    
                                }
                            }
                        }
                        /*
                        $userNew = User::join('users_subscriptions as us','us.user_id','users.id')
                        ->join('subscriptions as s','s.id','us.subscription_id')
                        ->where('users.id',$user->id)
                        ->select('users.id', 'users.name', 'users.lastname','users.password', 'users.email', 'users.number_phone', 'users.image', 'users.device_identifier','users.state','us.date_expiry','us.temporary_extension_end','us.temporary_extension_end','s.name as type_subscriptions')->first();
                        */
                        $userNew = User::leftjoin('users_subscriptions as us','us.user_id','users.id')
                        ->leftjoin('subscriptions as s','s.id','us.subscription_id')
                        ->where('users.id',$user->id)
                        ->select('users.id', 'users.name', 'users.lastname','users.password', 'users.email', 'users.number_phone', 'users.image', 'users.device_identifier','users.state','users.user_type','users.is_update','us.date_expiry','us.temporary_extension_end','us.temporary_extension_end','s.name as type_subscriptions','users.payment_state')->first();
                        if($userNew!=null){
                            if($userNew->user_type==2 || $userNew->user_type==3){
                                
                                $subscriptions = UserDependencyM::join('users_subscriptions as us', 'us.id', '=', 'user_dependency.user_subscription_id')
                                    ->join('subscriptions as s','s.id','us.subscription_id')
                                    ->where('user_dependency.dependent_user_id', $userNew->id)
                                    ->select('us.date_expiry','us.temporary_extension_end','us.temporary_extension_end','s.name as type_subscriptions')
                                    ->first();
                                
                                $userNew->date_expiry = $subscriptions->date_expiry;
                                $userNew->temporary_extension_end = $subscriptions->temporary_extension_end;
                                $userNew->temporary_extension_end = $subscriptions->temporary_extension_end;
                                $userNew->type_subscriptions = $subscriptions->type_subscriptions;
                            }

                        }
                                
                        
                        if($newIdentifier != null){
                            if($newIdentifier == $userNew->device_identifier){
                                if($userNew->temporary_extension_end!==null){
                                    if($userNew->temporary_extension_end >= $currentDate){                                  
                                        if($userNew->state==1 && $userNew->payment_state==1){                                            
                                            $userFull = User::select(['id', 'name', 'lastname', 'email', 'number_phone', 'image', 'device_identifier'])
                                            ->where('id', $user->id)
                                            ->without(['profile_photo_url'])->first();                                            
                                            return response()->json([
                                                "response" => true,
                                                "data" => $this->appendMobilePermissions($userFull),
                                                "failed" => 'no',
                                                "type_subscriptions" => $userNew->type_subscriptions,
                                            ]);

                                        }else{
                                            return response()->json([
                                                "response" => false,
                                                "data" => null,
                                                "failed" => 'unique constraint',
                                            ]);
                                        }
                                    }else{
                                        return response()->json([
                                            "response" => false,
                                            "data" => null,
                                            "failed" => 'user inactive due to subscription no payment link',
                                            "type_subscriptions" => $userNew->type_subscriptions,
                                        ]);
                                    }
                                }else{

                                    if($userNew->date_expiry >= $currentDate){                                  
                                        if($userNew->state==1 && $userNew->payment_state==1){
                                            
                                            $userFull = User::select(['id', 'name', 'lastname', 'email', 'number_phone', 'image', 'device_identifier'])
                                            ->where('id', $user->id)
                                            ->without(['profile_photo_url'])->first();
                                            
                                            return response()->json([
                                                "response" => true,
                                                "data" => $this->appendMobilePermissions($userFull),
                                                "failed" => 'no',
                                                "type_subscriptions" => $userNew->type_subscriptions,
                                            ]);
                                        }else{
                                            return response()->json([
                                                "response" => false,
                                                "data" => null,
                                                "failed" => 'unique constraint',
                                            ]);
                                        }
                                    }else{
                                        return response()->json([
                                            "response" => false,
                                            "data" => null,
                                            "failed" => 'user inactive due to subscription no payment link',
                                            "type_subscriptions" => $userNew->type_subscriptions,
                                        ]);
                                    }

                                }

                            }else{
                                return response()->json([
                                    "response" => false,
                                    "data" => null,
                                    "failed" => 'mobile token does not match',
                                    "type_subscriptions" => $userNew->type_subscriptions,
                                ]);
                            }
                        }else{                            

                            $position = strrpos($userNew->device_identifier, '_');
                            $identifier_whithout_ID = substr($userNew->device_identifier, 0, $position);

                            if($request->identifier == $userNew->device_identifier || $request->identifier == $identifier_whithout_ID){
                                if($userNew->temporary_extension_end!==null){
                                    if($userNew->temporary_extension_end>=$currentDate){                                  
                                        if($userNew->state==1 && $userNew->payment_state==1){
                                            
                                            $userFull = User::select(['id', 'name', 'lastname', 'email', 'number_phone', 'image', 'device_identifier'])
                                            ->where('id', $user->id)
                                            ->without(['profile_photo_url'])->first();
                                            
                                            return response()->json([
                                                "response" => true,
                                                "data" => $this->appendMobilePermissions($userFull),
                                                "failed" => 'no',
                                                "type_subscriptions" => $userNew->type_subscriptions,
                                            ]);
                                        }else{
                                            return response()->json([
                                                "response" => false,
                                                "data" => null,
                                                "failed" => 'unique constraint',
                                                "type_subscriptions" => $userNew->type_subscriptions,
                                            ]);
                                        }
                                    }else{
                                        return response()->json([
                                            "response" => false,
                                            "data" => null,
                                            "failed" => 'user inactive due to subscription no payment link',
                                            "type_subscriptions" => $userNew->type_subscriptions,
                                        ]);
                                    }
                                }else{
                                    if($userNew->date_expiry>=$currentDate){                                  
                                        if($userNew->state==1 && $userNew->payment_state==1){
                                            
                                            $userFull = User::select(['id', 'name', 'lastname', 'email', 'number_phone', 'image', 'device_identifier'])
                                            ->where('id', $user->id)
                                            ->without(['profile_photo_url'])->first();
                                            
                                            return response()->json([
                                                "response" => true,
                                                "data" => $this->appendMobilePermissions($userFull),
                                                "failed" => 'no',
                                                "type_subscriptions" => $userNew->type_subscriptions,
                                            ]);
                                        }else{
                                            return response()->json([
                                                "response" => false,
                                                "data" => null,
                                                "failed" => 'unique constraint',
                                                "type_subscriptions" => $userNew->type_subscriptions,
                                            ]);
                                        }
                                    }else{
                                        return response()->json([
                                            "response" => false,
                                            "data" => null,
                                            "failed" => 'user inactive due to subscription no payment link',
                                            "type_subscriptions" => $userNew->type_subscriptions,
                                        ]);
                                    }

                                }
                            }else{
                                return response()->json([
                                    "response" => false,
                                    "data" => null,
                                    "failed" => 'mobile token does not match',
                                    "type_subscriptions" => $userNew->type_subscriptions,
                                ]);
                            }
                        }
                        
                    }else{
                        return response()->json([
                            "response" => false,
                            "data" => null,
                            "failed" => 'incorrect credentials',
                            "type_subscriptions" => $user->type_subscriptions,
                        ]);
                    }
                //}else{
                    //return response()->json([
                        //"response" => false,
                        //"data" => null,
                        //"failed" => 'not a subscriber',
                    //]);
                //}
            }else{
                return response()->json([
                    "response" => false,
                    "data" => null,
                    "failed"=> 'does not exist',
                    "type_subscriptions" => null,
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                "response" => false,
                "data" => null,
                "failed"=> $th->getMessage(),
                "type_subscriptions" => null,
            ]);
        }
    }

    private function appendMobilePermissions(User $user): User
    {
        $user->setAttribute(
            'is_manager_role',
            $user->hasAnyRole(self::MANAGER_ROLE_NAMES)
        );

        return $user;
    }
}
