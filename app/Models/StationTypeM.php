<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SensorM;
class StationTypeM extends Model
{
    use HasFactory;
    protected $table = 'stations_type';
    protected $fillable = [
        'id',
        'name',
        'state',
    ];

    public function sensors()
    {
        return $this->belongsToMany(SensorM::class, 'type_sensors', 'station_type_id', 'sensor_id');
    }
}
