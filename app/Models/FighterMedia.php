<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FighterMedia extends Model
{
    protected $table = 'fighter_media';

    protected $guarded = ['id'];

    protected $casts = [
        'is_featured' => 'boolean',
        'status' => 'integer',
        'display_order' => 'integer',
    ];

    public function fighter()
    {
        return $this->belongsTo(Fighter::class);
    }
}
