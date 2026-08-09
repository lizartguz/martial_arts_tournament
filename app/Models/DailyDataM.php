<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StationM;

class DailyDataM extends Model
{
    use HasFactory;
    protected $table = 'daily_data';
    protected $fillable = [
        'registration_date',
        'year',
        'month',
        'day',
        'tempin',
        'tempout',
        'tempoutmin',
        'tempoutmax',
        'total_tempout',
        'humin',
        'humout',
        'humoutmin',
        'humoutmax',
        'total_humout',
        'dewptout',
        'dewptin',
        'heatindexout',
        'press',
        'seapress',
        'windchill',
        'windavg',
        'windspeed',
        'winddir',
        'windspeedhi',
        'winddirhi',
        'winddirstr',
        'windspeed2',
        'winddir2',
        'windspeedhi2',
        'winddirhi2',
        'winddirstr2',
        'rainrate',
        'raintotal',
        'total_rainrate',
        'uvindex',
        'solrad',
        'solradhi',
        'solevo',
        'solradhi',
        'soiltemp1',
        'soiltemp2',
        'soiltemp3',
        'soiltemp4',
        'soilhum1',
        'soilhum2',
        'soilhum3',
        'soilhum4',
        'leaftemp1',
        'leaftemp2',
        'leaftemp3',
        'leaftemp4',
        'leafhum1',
        'leafhum2',
        'leafhum3',
        'leafhum4',
        'total_leafhum1',
        'total_leafhum2',
        'total_leafhum3',
        'total_leafhum4',
        'pm10',
        'pm2_5',
        'pm1',
        'state',
        'station_id',
    ];
   
    public function station()
    {
        return $this->belongsTo(StationM::class, 'station_id', 'id');
    }
}