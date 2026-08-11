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

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
