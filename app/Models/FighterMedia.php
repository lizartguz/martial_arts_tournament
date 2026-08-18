<?php

namespace App\Models;

use App\Support\PublicMedia;
use Illuminate\Database\Eloquent\Model;

class FighterMedia extends Model
{
    protected $table = 'fighter_media';

    protected $guarded = ['id'];

    protected $casts = [
        'is_featured' => 'boolean',
        'status' => 'integer',
        'display_order' => 'integer',
    ];

    /**
     * Relaciona peleador asociado.
     */
    public function fighter()
    {
        return $this->belongsTo(Fighter::class);
    }

    /**
     * Devuelve la URL pública controlada del archivo de imagen.
     */
    public function fileUrl(): ?string
    {
        return $this->file_type === 'image' ? PublicMedia::url($this->file_path) : $this->file_path;
    }
}
