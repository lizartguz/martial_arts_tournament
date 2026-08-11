<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    use HasFactory;

    protected $table = 'fcm_tokens';

    protected $fillable = [
        'user_id',
        'token',
        'platform',
        'device_identifier',
        'device_name',
        'browser',
        'delivery_platform',
        'last_seen_at',
        'last_sent_at',
        'last_error_at',
        'last_error_message',
        'invalidated_at',
        'is_active',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'last_sent_at' => 'datetime',
        'last_error_at' => 'datetime',
        'invalidated_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relaciona usuario asociado.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
