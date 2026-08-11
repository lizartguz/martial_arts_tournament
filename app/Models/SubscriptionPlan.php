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

    /**
     * Relaciona beneficios del plan.
     */
    public function planFeatures()
    {
        return $this->hasMany(SubscriptionPlanFeature::class)->orderBy('display_order')->orderBy('id');
    }

    /**
     * Relaciona suscripciones de usuarios asociadas.
     */
    public function userSubscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Relaciona solicitudes de compra asociadas.
     */
    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class, 'subscription_plan_id');
    }
}
