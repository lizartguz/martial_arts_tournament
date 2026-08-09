<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StationM;

class AlertM extends Model
{
    use HasFactory;
    protected $table = 'alerts';
    protected $fillable = [
        'id',
        'description',
        'reg_date',
        'f',
        'station_id',
        'state',       
    ];
   
    public function station()
    {
        return $this->belongsTo(StationM::class, 'station_id', 'id');
    }    
}
