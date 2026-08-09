<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StationM;

class CurrentDataM extends Model
{
    use HasFactory;
    protected $table = 'current_data';
    protected $fillable = [
        'receipt_date',
        'registration_date',
        'tempin',
        'tempout',
        'tempoutmin',
        'tempoutmax',
        'humin',
        'humout',
        'humoutmin',
        'humoutmax',
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
        'uvindex',
        'solrad',
        'solradhi',
        'solevo',
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
        'pm10',
        'pm2_5',
        'pm1',
        'gas_ppm',
        'state',
        'station_id',
    ];
   
    public function station()
    {
        return $this->belongsTo(StationM::class, 'station_id', 'id');
    }
}
