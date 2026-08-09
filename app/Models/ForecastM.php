<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForecastM extends Model
{
    use HasFactory;
    protected $table = 'forecasts';
    protected $fillable = [
        'id',
        'reg_date',
        'v10m',
        'v10m_dir',
        'v10m_max',
        'v850',
        'v850_dir',
        'prec',
        'cape',
        'li',
        'dam',
        'a850',
        'a500',
        't2m',
        'hr2m',
        'hr2m_max',
        'hr2m_min',
        't850',
        't500',
        'baro',
        'cloud',
        'snow',
        'dp',
        'tmax',
        'tmin',
        'prec_total',
        'sh_ts',
        'icon',
        'icon_image',
        'min_t2m',
        'min_dp',
        'state',
        'station_id',
    ]; 
    
    public function station(){        
        return $this->belongsTo(StationM::class, 'station_id', 'id');
    }
}
