<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SensorM;
use App\Models\StationTypeM;
class TypeSensorM extends Model
{
    use HasFactory;
    protected $table = 'type_sensors';
    protected $fillable = [
        'id',
        'station_type_id',
        'sensor_id',       
        'state',
    ];

    public function sensor()
    {
        return $this->belongsTo(SensorM::class, 'sensor_id');
    }

    public function stationType()
    {
        return $this->belongsTo(StationTypeM::class, 'station_type_id');
    }
}