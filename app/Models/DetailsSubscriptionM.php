<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SubscriptionM;
class DetailsSubscriptionM extends Model
{
    use HasFactory;

    protected $table = 'details_subscriptions';
    protected $fillable = [
        'description',
        'subscription_id',
    ];
   
    public function station()
    {
        return $this->belongsTo(SubscriptionM::class, 'subscription_id', 'id');
    }
}
