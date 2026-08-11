<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'renewal_at' => 'datetime',
        'status' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Relaciona usuario asociado.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relaciona plan asociado.
     */
    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id')->withTrashed();
    }

    /**
     * Relaciona pagos asociados.
     */
    public function payments()
    {
        return $this->hasMany(SubscriptionPayment::class, 'user_subscription_id');
    }

    /**
     * Relaciona usuario creador.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
