<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FighterTeam extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
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
     * Relaciona peleadores asociados.
     */
    public function fighters()
    {
        return $this->hasMany(Fighter::class);
    }

    /**
     * Devuelve la URL pública del logo.
     */
    public function logoUrl(): ?string
    {
        return $this->logo_path ? asset($this->logo_path) : null;
    }
}
