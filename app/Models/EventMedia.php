<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventMedia extends Model
{
    protected $table = 'event_media';

    protected $guarded = ['id'];

    protected $casts = [
        'is_featured' => 'boolean',
        'display_order' => 'integer',
        'status' => 'integer',
    ];

    /**
     * Relaciona evento asociado.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Relaciona usuario creador.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
