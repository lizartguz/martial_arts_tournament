<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDownloadColumnPreferenceM extends Model
{
    protected $table = 'user_download_column_preferences';

    protected $fillable = [
        'user_id',
        'selected_columns',
    ];

    protected $casts = [
        'selected_columns' => 'array',
    ];
}
