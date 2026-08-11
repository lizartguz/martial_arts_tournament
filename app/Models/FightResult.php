<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FightResult extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'round' => 'integer',
    ];

    public function fight()
    {
        return $this->belongsTo(Fight::class);
    }

    public function winner()
    {
        return $this->belongsTo(Fighter::class, 'winner_fighter_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
