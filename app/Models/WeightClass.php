<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeightClass extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'min_weight_kg' => 'decimal:2',
        'max_weight_kg' => 'decimal:2',
        'max_weight_lb' => 'decimal:2',
        'display_order' => 'integer',
        'status' => 'integer',
    ];

    /**
     * Relaciona peleadores asociados.
     */
    public function fighters()
    {
        return $this->hasMany(Fighter::class);
    }

    /**
     * Relaciona combates asociados.
     */
    public function fights()
    {
        return $this->hasMany(Fight::class);
    }

    /**
     * Relaciona rankings asociados.
     */
    public function rankings()
    {
        return $this->hasMany(FighterRanking::class);
    }
}
