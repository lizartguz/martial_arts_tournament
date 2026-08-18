<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Relaciona país asociado.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
