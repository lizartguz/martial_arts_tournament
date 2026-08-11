<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPlan extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'features' => 'array',
        'limits' => 'array',
        'display_order' => 'integer',
        'status' => 'integer',
    ];

    public function planFeatures()
    {
        return $this->hasMany(SubscriptionPlanFeature::class)->orderBy('display_order')->orderBy('id');
    }

    public function userSubscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class, 'subscription_plan_id');
    }
}
