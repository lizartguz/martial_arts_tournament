<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AppUpdateM extends Model
{
    use HasFactory;
    protected $table = 'app_update';
    protected $fillable = [
        'id',
        'update_message_android',
        'update_message_ios',
        'version_android',
        'version_ios',        
        'block_android',
        'block_ios',
        'current_version_android_text',        
        'current_version_ios_text',
        'new_version_android_text',
        'new_version_ios_text',
        'link_android',
        'link_ios',
        'state',
        'user_id',
    ];  

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}