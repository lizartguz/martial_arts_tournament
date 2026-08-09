<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StationM;

class DbCurrentYearM extends Model
{
    use HasFactory;
    protected $connection = 'db_current_year'; 
    protected $table = 'data';
    
   
    public function station()
    {
        return $this->belongsTo(StationM::class, 'station_id', 'id');
    }
}
