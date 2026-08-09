<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkCredentialM extends Model
{
    use HasFactory;
    
    protected $connection = 'mysql'; 
    protected $table = 'network_credentials';
    
    protected $fillable = [
        'id',
        'ssid',
        'password',
        'station_id',
        'user_creator_id',
        'state',
        'sent_to_device',
    ];

    /**
     * Relación con Station
     */
    public function station()
    {
        return $this->belongsTo(StationM::class, 'station_id', 'id');
    }

    /**
     * Relación con User (creador)
     */
    public function userCreator()
    {
        return $this->belongsTo(User::class, 'user_creator_id', 'id');
    }

    /**
     * Relación con NetworkCredentialHistory
     */
    public function histories()
    {
        return $this->hasMany(NetworkCredentialHistoryM::class, 'network_credential_id', 'id');
    }
}

