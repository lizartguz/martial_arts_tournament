<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fight extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'starts_at' => 'datetime',
        'rounds' => 'integer',
        'display_order' => 'integer',
        'status' => 'integer',
        'is_main_event' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function cornerRed()
    {
        return $this->belongsTo(Fighter::class, 'corner_red_fighter_id');
    }

    public function cornerBlue()
    {
        return $this->belongsTo(Fighter::class, 'corner_blue_fighter_id');
    }

    public function weightClass()
    {
        return $this->belongsTo(WeightClass::class);
    }

    public function result()
    {
        return $this->hasOne(FightResult::class);
    }

    public function promoImageUrl(): ?string
    {
        return $this->promo_image ? asset($this->promo_image) : null;
    }
}
