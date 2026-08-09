<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkCredentialHistoryM extends Model
{
    use HasFactory;
    
    protected $connection = 'mysql'; 
    protected $table = 'network_credentials_history';
    
    protected $fillable = [
        'id',
        'ssid',
        'password',
        'state',
        'network_credential_id',
        'user_creator_id',
    ];

    /**
     * Relación con NetworkCredential
     */
    public function networkCredential()
    {
        return $this->belongsTo(NetworkCredentialM::class, 'network_credential_id', 'id');
    }

    /**
     * Relación con User (creador)
     */
    public function userCreator()
    {
        return $this->belongsTo(User::class, 'user_creator_id', 'id');
    }
}

