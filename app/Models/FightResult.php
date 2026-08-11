<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FightResult extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'round' => 'integer',
    ];

    /**
     * Relaciona combate asociado.
     */
    public function fight()
    {
        return $this->belongsTo(Fight::class);
    }

    /**
     * Relaciona ganador asociado.
     */
    public function winner()
    {
        return $this->belongsTo(Fighter::class, 'winner_fighter_id');
    }

    /**
     * Relaciona usuario creador.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relaciona usuario que actualizó el registro.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
