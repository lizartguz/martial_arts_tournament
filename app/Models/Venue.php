<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venue extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'capacity' => 'integer',
        'status' => 'integer',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
