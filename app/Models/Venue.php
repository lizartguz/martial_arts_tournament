<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venue extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'capacity' => 'integer',
        'status' => 'integer',
    ];

    /**
     * Relaciona ciudad asociada.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Relaciona eventos asociados.
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
