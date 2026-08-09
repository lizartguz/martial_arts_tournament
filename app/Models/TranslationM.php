<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StationM;
use App\Models\User;

class TranslationM extends Model
{
    use HasFactory;
    protected $table = 'translations';
    protected $fillable = [
        'reg_date',
        'description',
        'state',
        'station_id',
        'user_id',        
    ];
   
    public function station()
    {
        return $this->belongsTo(StationM::class, 'station_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
