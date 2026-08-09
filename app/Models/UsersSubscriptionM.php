<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersSubscriptionM extends Model
{
    use HasFactory;

    protected $table = 'users_subscriptions';

    protected $fillable = [
        'business_name',
        'nit',
        'reg_date',
        'accumulation_date',
        'start_date',
        'expiration_notice',
        'date_expiry',
        'amount',
        'is_discount',
        'added_user_count',
        'added_user_total_cost',
        'cost',
        'latitude',
        'longitude',
        'invitation_reference_data',
        'temporary_extension_start',
        'temporary_extension_end',
        'incident_extension',
        'data_save',
        'is_it_paid_user',//0:pendiente 1:pagado
        'payment_type_user', //0:ninguno 1:efectivo 2:tarjeta 3:Qr 4:cortesia(sin pagar) 5:otro
        'is_it_paid_renewal',//0:pendiente 1:pagado
        'payment_type_renewal', //0:ninguno 1:efectivo 2:tarjeta 3:Qr 4:cortesia(sin pagar) 5:otro
        'state',
        'user_id',
        'subscription_id',
        'station_id',
        'payment_link_renewal',
        'payment_link_user',
        'transaction_renewal_id',
        'transaction_user_id',
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function subscription()
    {
        return $this->belongsTo(SubscriptionM::class,'subscription_id','id');
    }

    public function station()
    {
        return $this->belongsTo(StationM::class,'station_id','id');
    }

    public function userDependency()
    {
        return $this->hasMany(UserDependencyM::class,'user_subscription_id','id');
    }
}
