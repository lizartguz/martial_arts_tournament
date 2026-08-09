<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StationCalibrationM extends Model
{
    use HasFactory;

    protected $table = 'station_calibrations';

    protected $fillable = [
        'station_id',
        'variable_name',
        'calibration_key',
        'calibration_params',
        'is_active',
    ];

    protected $casts = [
        'calibration_params' => 'array',
        'is_active' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function station()
    {
        return $this->belongsTo(StationM::class, 'station_id', 'id');
    }
}
