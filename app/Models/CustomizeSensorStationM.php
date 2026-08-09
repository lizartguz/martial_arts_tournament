<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomizeSensorStationM extends Model
{
    use HasFactory;
    protected $table = 'customize_sensor_station';
    protected $fillable = [
        'id',
        'station_id',
        'sensor_id',
        'stations_type_id',
        'is_enabled',       
        'state', 
    ];
}