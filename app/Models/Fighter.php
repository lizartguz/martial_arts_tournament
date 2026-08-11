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

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function weightClass()
    {
        return $this->belongsTo(WeightClass::class);
    }

    public function team()
    {
        return $this->belongsTo(FighterTeam::class, 'fighter_team_id');
    }

    public function stats()
    {
        return $this->hasOne(FighterStat::class);
    }

    public function rankings()
    {
        return $this->hasMany(FighterRanking::class);
    }

    public function media()
    {
        return $this->hasMany(FighterMedia::class);
    }

    public function redCornerFights()
    {
        return $this->hasMany(Fight::class, 'corner_red_fighter_id');
    }

    public function blueCornerFights()
    {
        return $this->hasMany(Fight::class, 'corner_blue_fighter_id');
    }

    public function profileImageUrl(): ?string
    {
        return $this->profile_image ? asset($this->profile_image) : null;
    }

    public function coverImageUrl(): ?string
    {
        return $this->cover_image ? asset($this->cover_image) : null;
    }
}
