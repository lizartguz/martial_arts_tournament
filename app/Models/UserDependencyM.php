<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StationM;

class UserDependencyM extends Model
{
    use HasFactory;
    protected $table = 'user_dependency';
    protected $fillable = [
        'dependent_user_id',
        'user_subscription_id',       
    ];
   
    public function user()
    {
        return $this->belongsTo(User::class, 'dependent_user_id', 'id');
    }
    
    public function subscription()
    {
        return $this->belongsTo(UsersSubscriptionM::class, 'user_subscription_id', 'id');
    }
}
