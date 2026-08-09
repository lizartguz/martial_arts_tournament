<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoardM extends Model
{
    use HasFactory;
    protected $table = 'boards';
    protected $fillable = [
        'name',
        'link',
        'total_views',
        'busy_views',
        'state',
        'user_creator_id',
    ];

    /**
     * Relación con el usuario creador.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_creator_id');
    }
}