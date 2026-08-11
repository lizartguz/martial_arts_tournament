<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sponsor extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'display_order' => 'integer',
        'status' => 'integer',
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_sponsor')
            ->withPivot(['placement', 'display_order'])
            ->withTimestamps();
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path ? asset($this->logo_path) : null;
    }
}
