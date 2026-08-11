<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FighterStat extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'metadata' => 'array',
    ];
}
