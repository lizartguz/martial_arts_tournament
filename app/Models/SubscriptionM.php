<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DetailsSuscriptionM;
use App\Models\User;

class SubscriptionM extends Model
{
    use HasFactory;
    protected $table = 'subscriptions';
    protected $fillable = [
        'id',
        'name',
        'tag',
        'price',
        'amount',
        'duration',
        'duration_discount',
        'duration_value_discount',
        'discount_value_stations',
        'number_stations_discount',       
        'state_de',
        'state_dd',
        'state',
    ];

    public function detailsSubscription()
    {
        return $this->hasMany(DetailsSubscriptionM::class, 'subscription_id', 'id');
    }

    public function userSubscriptions()
    {
        return $this->hasMany(UsersSubscriptionM::class, 'subscription_id', 'id');
    }
}