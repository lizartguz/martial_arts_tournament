<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $guarded = ['id'];

    /**
     * Relaciona ciudades asociadas.
     */
    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
