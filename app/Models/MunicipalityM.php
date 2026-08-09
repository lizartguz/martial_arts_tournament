<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StationM;

class MunicipalityM extends Model
{
    use HasFactory;
    protected $table = 'municipalities';
    protected $fillable = [
        'id',
        'name',
        'state',
        'province_id',
    ];
   
   
}
