<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationM extends Model
{
    use HasFactory;
    protected $table = 'notifications';
    protected $fillable = [
        'id',
        'reg_date',
        'title',
        'description',
        'image',
        'image_1',
        'image_2',
        'link',
        'type_user',/* all, internal, external, group, individual */
        'type',/* push, normal, both */
        'delivery_platform',
        'deadline', //bulletin viewing deadline
        'scheduled_at',
        'push_sent_at',
        'push_last_error_at',
        'push_last_error_message',
        'metadata',
        'state',
        'creator_user_id',       
    ];

    protected $casts = [
        'metadata' => 'array',
        'reg_date' => 'datetime',
        'deadline' => 'date',
        'scheduled_at' => 'datetime',
        'push_sent_at' => 'datetime',
        'push_last_error_at' => 'datetime',
    ];
    
    /**
     * Relaciona usuario asociado.
     */
    public function user(){        
        return $this->belongsTo(User::class, 'creator_user_id', 'id');
    }
}
