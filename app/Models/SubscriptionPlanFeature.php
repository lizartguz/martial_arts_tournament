<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlanFeature extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'display_order' => 'integer',
        'status' => 'integer',
    ];

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
}
