<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\UsersSubscriptionM;
use App\Models\UserDependencyM;
use App\Models\User;

class UsersSubscriptionsC extends Controller
{
    public function index(){
        return view('user_subscriptions.index');
    }

    public function reportItem(Request $request){
        $id = $request->query('id');
        $type = $request->query('type');
        $hoy = date('Y-m-d H:i:s');
        $textOuput = "suscripcion_de_cliente.pdf";

        

        $usersSubscription = UserDependencyM::with(['user', 'subscription'])->where('user_subscription_id',$id)->first();

       /* $usersSubscription =  UserDependencyM::leftJoin('users_subscriptions as us', 'user_dependency.user_subscription_id', 'us.id')
                ->leftJoin('users as u', 'user_dependency.dependent_user_id', 'u.id')
                ->where('u.user_type', 1)
                ->select(
                    'u.id as userId',
                    'u.name',
                    'u.lastname',
                    'u.email',
                    'u.lastname',
                    'u.number_phone',
                    'u.state',
                    'us.id',
                    'us.start_date',
                    'us.date_expiry',
                    'us.temporary_extension_start',
                    'us.temporary_extension_end',
                    'us.is_it_paid_user',
                    'us.payment_type_user',
                    'us.is_it_paid_renewal',
                    'us.payment_type_renewal',
                )
                ->first();*/

        if($type == 'watch'){
            return Pdf::loadView('user_subscriptions.pdf_item',compact('usersSubscription'))
                    ->setPaper('a4', 'portrait')
                    ->stream($textOuput);
        }else if($type == 'download'){
            return Pdf::loadView('user_subscriptions.pdf_item',compact('usersSubscription'))
                    ->setPaper('a4', 'portrait')
                    ->download($textOuput);
        }        
    }
}
