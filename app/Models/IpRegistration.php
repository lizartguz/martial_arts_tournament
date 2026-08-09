<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpRegistration extends Model
{
    protected $fillable = ['ip', 'date', 'attempts'];

    public $timestamps = true;

    protected $casts = [
        'date' => 'date',
    ];
}
