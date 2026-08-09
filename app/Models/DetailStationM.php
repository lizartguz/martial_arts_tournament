<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StationM;

class DetailStationM extends Model
{
    use HasFactory;

    protected $table = 'details_stations';
    public $timestamps = true;
    protected $fillable = [
        'image1',
        'image2',
        'image3',
        'brand',
        'power_supply',
        //'reception_type',
        'model',
        'installation_date',
        'elevation',
        'installer_user_id',
        'station_model_id',
        'station_id',
        'state',
    ];

    public function station()
    {
        return $this->belongsTo(StationM::class, 'station_id', 'id');
    }

    public function stationModel()
    {
        return $this->belongsTo(StationModelM::class, 'station_model_id', 'id');
    }
}
