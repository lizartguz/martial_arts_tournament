<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCredit extends Model
{
    use HasFactory;

    protected $table = 'user_credits';

    protected $fillable = [
        'type',
        'used_credit',
        'total_credit',
        'state',
        'user_id',
    ];

    protected $casts = [
        'used_credit' => 'integer',
        'total_credit' => 'integer',
        'state' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


