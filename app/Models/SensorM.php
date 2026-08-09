<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StationTypeM;
class SensorM extends Model
{
    use HasFactory;
    protected $table = 'sensors';
    protected $fillable = [
        'id',
        'name',
        'abbreviation',
        'abbreviation2',
        'state',
    ];

    public function stationTypes()
    {
        return $this->belongsToMany(StationTypeM::class, 'type_sensors', 'sensor_id', 'station_type_id');
    }
}