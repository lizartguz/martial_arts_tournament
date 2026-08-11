<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fighter extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'birthdate' => 'date',
        'height_cm' => 'decimal:2',
        'reach_cm' => 'decimal:2',
        'wins' => 'integer',
        'losses' => 'integer',
        'draws' => 'integer',
        'no_contests' => 'integer',
        'status' => 'integer',
    ];

    /**
     * Relaciona país asociado.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Relaciona ciudad asociada.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Relaciona categoría de peso asociada.
     */
    public function weightClass()
    {
        return $this->belongsTo(WeightClass::class);
    }

    /**
     * Relaciona equipo asociado.
     */
    public function team()
    {
        return $this->belongsTo(FighterTeam::class, 'fighter_team_id');
    }

    /**
     * Relaciona estadísticas deportivas.
     */
    public function stats()
    {
        return $this->hasOne(FighterStat::class);
    }

    /**
     * Relaciona rankings asociados.
     */
    public function rankings()
    {
        return $this->hasMany(FighterRanking::class);
    }

    /**
     * Relaciona archivos multimedia asociados.
     */
    public function media()
    {
        return $this->hasMany(FighterMedia::class);
    }

    /**
     * Relaciona combates como esquina roja.
     */
    public function redCornerFights()
    {
        return $this->hasMany(Fight::class, 'corner_red_fighter_id');
    }

    /**
     * Relaciona combates como esquina azul.
     */
    public function blueCornerFights()
    {
        return $this->hasMany(Fight::class, 'corner_blue_fighter_id');
    }

    /**
     * Devuelve la URL pública de la imagen de perfil.
     */
    public function profileImageUrl(): ?string
    {
        return $this->profile_image ? asset($this->profile_image) : null;
    }

    /**
     * Devuelve la URL pública de la imagen de portada.
     */
    public function coverImageUrl(): ?string
    {
        return $this->cover_image ? asset($this->cover_image) : null;
    }
}
