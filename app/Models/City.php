<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $guarded = ['id'];

    /**
     * Relaciona país asociado.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
