<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FighterRanking extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'position' => 'integer',
        'previous_position' => 'integer',
        'is_champion' => 'boolean',
        'ranked_at' => 'date',
        'status' => 'integer',
    ];

    public function fighter()
    {
        return $this->belongsTo(Fighter::class);
    }

    public function weightClass()
    {
        return $this->belongsTo(WeightClass::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
