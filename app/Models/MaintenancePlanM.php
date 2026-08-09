<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenancePlanM extends Model
{
    use HasFactory;
    protected $table = 'maintenance_plan';
    protected $fillable = [
        'id',
        'maintenance_date',
        'next_maintenance_date',
        'delivery_note_number',
        'work_order_number',
        'preventive_maintenance',
        'corrective_maintenance',
        'description',
        'recommendations',
        'image_before',
        'image_after',
        'image_signature',      
        'state',
        'user_id',
        'modified_by_user_id',
        'station_id',
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
