<?php

namespace App\Http\Controllers\Api\v1;

use DateTime;
use App\Models\User;
use App\Models\StationM;
use Illuminate\Http\Request;
use App\Models\UserDependencyM;
use App\Models\UsersSubscriptionM;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class UserPermissionVerif extends Controller
{
    private const MANAGER_ROLE_NAMES = [
        'super_manager',
        'manager',
        'meteorology',
        'meteorologist',
        'technical',
    ];

    static public function verif2($id,$email,$phone,$identifier){

        $userPermission = 'no';
        //$userPermission = 'yes';
        $detailPermission ='';
        $typeUser='';
        try {
            DB::beginTransaction();
            $user = null;
            if($email!='' && $phone!=''){
                if($phone!=''){
                    $user = User::where('id',$id)->where('email',$email)->where('number_phone',$phone)->first();
                    if($user!=null){
                        $typeUser='user';
                    }
                }

            }else if($email!=''){
                $user = User::where('id',$id)->where('email',$email)->first();
                    if($user!=null){
                        $typeUser='user';
                    }
            }else{
                if($phone!=''){
                    $user = User::where('id',$id)->where('number_phone',$phone)->first();
                    if($user!=null){
                        $typeUser='user';
                    }
                }
            }

            if($user!=null){
                if($user->own_state==1){
                    if($user->state==1){
                        if($user->device_identifier!=null){
                            if($user->device_identifier == $identifier){
                                $userPermission = 'yes';
                            }else{
                                //token movil no es igual
                                $detailPermission ='mobile token is not the same';
                            }
                        }else{
                            //token movil es nulo
                            $detailPermission ='mobile token is null';
                        }
                    }else{
                        //usuario inactivo
                        $detailPermission ='inactive user';
                    }
                }else{
                    //usuario inactivo por subscripcion
                    $detailPermission ='user inactive due to subscription no payment link';
                }
            } else{
                //no existe el usuario
                $detailPermission ='user does not exist';
            }
            DB::commit();

        } catch (\Throwable $th) {
            DB::rollBack();
        }

        return [$userPermission,$detailPermission,$typeUser];
    }


    

    static public function verif($id,$email,$phone,$identifier){
        $userPermission = 'no';
        //$userPermission = 'yes';
        $detailPermission ='';
        $typeUserSubscript='';
        $userData = null;
        try {
            $currentDate = date('Y-m-d');
            DB::beginTransaction();
            $user = null;
            if($email!='' && $phone!=''){                
                /*
                $user = User::join('users_subscriptions as us','us.user_id','users.id')
                        ->join('subscriptions as s','s.id','us.subscription_id')
                        ->where('users.id',$id)->where('users.email',$email)->where('users.number_phone',$phone)
                        ->select('users.id', 'users.name', 'users.lastname', 'users.email', 'users.number_phone','users.device_identifier','users.state','us.date_expiry','us.temporary_extension_end','us.temporary_extension_end','s.name as type_subscriptions')->first();
                */
                $user = User::leftjoin('users_subscriptions as us','us.user_id','users.id')
                ->leftjoin('subscriptions as s','s.id','us.subscription_id')
                ->where('users.id',$id)->where('users.email',$email)->where('users.number_phone',$phone)
                ->select('users.id', 'users.name', 'users.lastname', 'users.email', 'users.number_phone','users.device_identifier','users.user_type','users.state','us.date_expiry','us.temporary_extension_end','us.temporary_extension_end','s.name as type_subscriptions','users.payment_state')->first();
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
                
                $typeUserSubscript = $user->type_subscriptions ?? '';                
            }else if($email!=''){
                /*
                $user = User::join('users_subscriptions as us','us.user_id','users.id')
                        ->join('subscriptions as s','s.id','us.subscription_id')
                        ->where('users.id',$id)->where('users.email',$email)
                        ->select('users.id', 'users.name', 'users.lastname', 'users.email', 'users.number_phone','users.device_identifier','users.state','us.date_expiry','us.temporary_extension_end','us.temporary_extension_end','s.name as type_subscriptions')->first();
                */
                $user = User::leftjoin('users_subscriptions as us','us.user_id','users.id')
                ->leftjoin('subscriptions as s','s.id','us.subscription_id')
                ->where('users.id',$id)->where('users.email',$email)
                ->select('users.id', 'users.name', 'users.lastname', 'users.email', 'users.number_phone','users.device_identifier','users.user_type','users.state','us.date_expiry','us.temporary_extension_end','us.temporary_extension_end','s.name as type_subscriptions','users.payment_state')->first();
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

                $typeUserSubscript = $user->type_subscriptions ?? '';                   
            }else{
                if($phone!=''){
                    /*
                    $user = User::join('users_subscriptions as us','us.user_id','users.id')
                        ->join('subscriptions as s','s.id','us.subscription_id')
                        ->where('users.id',$id)->where('users.number_phone',$phone)
                        ->select('users.id', 'users.name', 'users.lastname', 'users.email', 'users.number_phone','users.device_identifier','users.state','us.date_expiry','us.temporary_extension_end','us.temporary_extension_end','s.name as type_subscriptions')->first();
                    */
                    $user = User::leftjoin('users_subscriptions as us','us.user_id','users.id')
                    ->leftjoin('subscriptions as s','s.id','us.subscription_id')
                    ->where('users.id',$id)->where('users.number_phone',$phone)
                    ->select('users.id', 'users.name', 'users.lastname', 'users.email', 'users.number_phone','users.device_identifier','users.user_type','users.state','us.date_expiry','us.temporary_extension_end','us.temporary_extension_end','s.name as type_subscriptions','users.payment_state')->first();
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
                    $typeUserSubscript = $user->type_subscriptions ?? '';
                }
            }
            if($user!=null){
                $userData = self::appendManagerRoleFlag($user);
                if($user->device_identifier!=null){
                    if($user->device_identifier == $identifier){
                        if($user->temporary_extension_end!==null){
                            if($user->temporary_extension_end >= $currentDate){ 
                                if($user->state==1 && $user->payment_state==1){
                                    $userPermission = 'yes';
                                }else{
                                    //usuario inactivo
                                    $detailPermission ='inactive user';
                                }
                            }else{
                                //usuario suscripcion caducada
                                //$detailPermission ='user inactive due to subscription no payment link';
                                $detailPermission ='user inactive due to subscription';
                            }
                        }else{
                            if($user->date_expiry >= $currentDate){ 
                                if($user->state==1 && $user->payment_state==1){
                                    $userPermission = 'yes';
                                }else{
                                    //usuario inactivo
                                    $detailPermission ='inactive user';
                                }
                            }else{
                                //usuario suscripcion caducada
                                //$detailPermission ='user inactive due to subscription no payment link';
                                $detailPermission ='user inactive due to subscription';
                            }
                        }
                    }else{
                        //token movil no es igual
                        $detailPermission ='mobile token is not the same';
                    }
                }else{
                    //token movil es nulo
                    $detailPermission ='mobile token is null';
                }
            } else{
                $userData = User::where('id',$id)->select('id','username','name','lastname','email','number_phone','device_identifier','version_app','is_update')->first();
                
                if($userData != null && $userData->is_update === 'yes'){
                    $userData->update(['is_update' => 'no' ]);
                    DB::commit();
                    unset($userData->version_app);
                    unset($userData->is_update);
                    unset($userData->updated_at);
                    $userData = self::appendManagerRoleFlag($userData);
                    $userPermission = 'yes';
                }else{
                    //no existe el usuario
                    $detailPermission ='user does not exist';
                }          
            }
            DB::commit();

        } catch (\Throwable $th) {
            DB::rollBack();
        }

        return [$userPermission,$detailPermission,$typeUserSubscript,$userData];
    }

    private static function appendManagerRoleFlag(User $user): User
    {
        $user->setAttribute(
            'is_manager_role',
            $user->hasAnyRole(self::MANAGER_ROLE_NAMES)
        );

        return $user;
    }
    
}
