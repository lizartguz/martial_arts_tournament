<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StationModelM extends Model
{
    protected $table = 'station_models';
    protected $fillable = ['name', 'state', 'brand_id'];

    public function brand()
    {
        return $this->belongsTo(BrandM::class, 'brand_id', 'id');
    }

    public function detailStations()
    {
        return $this->hasMany(DetailStationM::class, 'station_model_id', 'id');
    }
}
