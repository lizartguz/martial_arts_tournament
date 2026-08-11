<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'social_links' => 'array',
        'landing_show_rankings' => 'boolean',
    ];
}
