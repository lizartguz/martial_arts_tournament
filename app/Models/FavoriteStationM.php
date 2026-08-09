<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteStationM extends Model
{
    use HasFactory;

    protected $table = 'favorite_stations';
    protected $fillable = [
        'state',
        'user_id',
        'sub_user_id',     
        'station_id',
    ];
   
    public function station()
    {
        return $this->belongsTo(StationM::class, 'station_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
