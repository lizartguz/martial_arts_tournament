<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'handled_at' => 'datetime',
        'status' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Relaciona evento asociado.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

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
     * Relaciona usuario responsable.
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Relaciona usuario que gestionó la solicitud.
     */
    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
