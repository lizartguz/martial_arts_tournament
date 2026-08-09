<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StationM extends Model
{
    use HasFactory;
    protected $connection = 'mysql'; 
    protected $table = 'stations';
    protected $fillable = [
        'id',
        'code',
        'name',
        'location',
        'address',
        'search_concatenation',
        'reg_date',
        'latitude',
        'longitude',
        'state',
        'et',
        'user_creator_id',
        'modifier_user_id',
        'municipality_id',
    ];

    public function calibrations()
    {
        return $this->hasMany(StationCalibrationM::class, 'station_id', 'id');
    }

    public function alerts()
    {
        return $this->hasMany(AlertM::class, 'station_id');
    }

    public function detail_station()
    {
        return $this->hasMany(DetailStationM::class, 'station_id', 'id');
    }

    public function user_creator()
    {
        return $this->belongsTo(User::class, 'user_creator_id', 'id');
    }
    

    public function modifier_user()
    {
        return $this->belongsTo(User::class, 'modifier_user_id', 'id');
    }

    public function municipality()
    {
        return $this->belongsTo(User::class, 'municipality', 'id');
    }    

    public function maintenance_plan()
    {
        return $this->hasMany(MaintenancePlanM::class,'station_id', 'id');
    }

    public function forecasts()
    {
        return $this->hasMany(ForecastM::class,'station_id', 'id');
    }
    

    public function favoriteStations()
    {
        return $this->hasMany(FavoriteStationM::class, 'station_id', 'id');
    }


    public function dataDbCurrentYear()
    {
        return $this->hasMany(DbCurrentYearM::class, 'station_id', 'id');
    }

    public function networkCredentials()
    {
        return $this->hasMany(NetworkCredentialM::class, 'station_id', 'id');
    }
}
