<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StationM;

class ProvinceM extends Model
{
    use HasFactory;
    protected $table = 'provinces';
    protected $fillable = [
        'id',
        'name',
        'state',
        'department_id',
    ];  
}