<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StationM;

class StationsNoticeM extends Model
{
    use HasFactory;
    protected $table = 'stations_notices';
    protected $fillable = [
        'title',
        'description',
        'reg_date',
        'state',
        'station_id',
    ];
   
    public function station()
    {
        return $this->belongsTo(StationM::class, 'station_id', 'id');
    }
}
