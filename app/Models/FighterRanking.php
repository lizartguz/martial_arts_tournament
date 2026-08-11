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

    /**
     * Relaciona peleador asociado.
     */
    public function fighter()
    {
        return $this->belongsTo(Fighter::class);
    }

    /**
     * Relaciona categoría de peso asociada.
     */
    public function weightClass()
    {
        return $this->belongsTo(WeightClass::class);
    }

    /**
     * Relaciona usuario creador.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
