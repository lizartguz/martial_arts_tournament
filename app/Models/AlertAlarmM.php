<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StationM;

class AlertAlarmM extends Model
{
    use HasFactory;
    protected $table = 'alerts_alarms';
    protected $fillable = [
        'id',
        'temp_max',
        'temp_min',
        'wind_max',
        'rain_total',
        'uv_max',
        'solar_rad_max',
        'drop_type',
        'dt_max',
        'dt_min',
        'type', 
        'dt_new',
        'dewpoint',
        'dewpoint_new',
        'notification_status',
        'state',
        'station_id',
        'user_id',       
    ];
   
    public function station()
    {
        return $this->belongsTo(StationM::class, 'station_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
