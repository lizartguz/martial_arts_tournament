<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SenvaDataM extends Model
{
    private const CONNECTION_PREFIX = 'senvatec_db_';

    protected $table = 'senva_data';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'station_id',
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
        'pm1',
        'pm2_5',
        'pm10',
        'gas_ppm',
        'state',
        'created_at',
        'updated_at',
    ];

    public function getConnectionName(): ?string
    {
        return $this->connection ?? self::CONNECTION_PREFIX . now()->year;
    }
}
