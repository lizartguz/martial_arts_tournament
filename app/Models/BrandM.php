<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandM extends Model
{
    protected $table = 'brands';
    protected $fillable = ['name', 'state'];

    public function stationModels()
    {
        return $this->hasMany(StationModelM::class, 'brand_id', 'id');
    }
}
