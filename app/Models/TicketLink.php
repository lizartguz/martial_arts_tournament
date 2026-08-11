<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketLink extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'price_from' => 'decimal:2',
        'status' => 'integer',
    ];

    /**
     * Relaciona evento asociado.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
