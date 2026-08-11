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

    /**
     * Relaciona evento asociado.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Relaciona peleador de la esquina roja.
     */
    public function cornerRed()
    {
        return $this->belongsTo(Fighter::class, 'corner_red_fighter_id');
    }

    /**
     * Relaciona peleador de la esquina azul.
     */
    public function cornerBlue()
    {
        return $this->belongsTo(Fighter::class, 'corner_blue_fighter_id');
    }

    /**
     * Relaciona categoría de peso asociada.
     */
    public function weightClass()
    {
        return $this->belongsTo(WeightClass::class);
    }

    /**
     * Relaciona resultado oficial asociado.
     */
    public function result()
    {
        return $this->hasOne(FightResult::class);
    }

    /**
     * Devuelve la URL pública de la imagen promocional.
     */
    public function promoImageUrl(): ?string
    {
        return $this->promo_image ? asset($this->promo_image) : null;
    }
}
