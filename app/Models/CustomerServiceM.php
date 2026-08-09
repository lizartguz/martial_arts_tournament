<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerServiceM extends Model
{
    protected $table = 'customer_service';
    protected $fillable = [
        'name',
        'lastname',
        'email',
        'company',
        'number_phone',
        'location',
        'country',
        'city',
        'title',
        'description',
        'type',
        'state',
    ];
 }
