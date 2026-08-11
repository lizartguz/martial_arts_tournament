<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsPost extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'status' => 'integer',
    ];

    /**
     * Define la clave usada para resolver rutas públicas.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Relaciona usuario creador.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relaciona usuario que actualizó el registro.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Devuelve la URL pública de la imagen de portada.
     */
    public function coverImageUrl(): ?string
    {
        return $this->cover_image ? asset($this->cover_image) : null;
    }

    /**
     * Filtra registros visibles públicamente.
     */
    public function scopePublished($query)
    {
        return $query
            ->where('status', 1)
            ->where(fn ($subQuery) => $subQuery
                ->whereNull('published_at')
                ->orWhere('published_at', '<=', now()));
    }

    /**
     * Valida si el registro puede mostrarse públicamente.
     */
    public function isPublished(): bool
    {
        return (int) $this->status === 1
            && (! $this->published_at || $this->published_at->lte(now()));
    }
}
