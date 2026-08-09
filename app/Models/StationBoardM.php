<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\StationM;

class StationBoardM extends Model
{
    use HasFactory;
    protected $table = 'station_boards';
    protected $fillable = [
        'total_views',
        'busy_views',
        'state',
        'station_id',
        'user_creator_id',
    ];

    /**
     * Relación con la estación.
     */
    public function station()
    {
        return $this->belongsTo(StationM::class, 'station_id');
    }

    /**
     * Relación con el usuario creador.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_creator_id');
    }
}
