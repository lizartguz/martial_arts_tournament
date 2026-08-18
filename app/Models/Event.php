<?php

namespace App\Models;

use App\Support\PublicMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'starts_at' => 'datetime',
        'doors_open_at' => 'datetime',
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
     * Devuelve información asociada a venue.
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Relaciona combates asociados.
     */
    public function fights()
    {
        return $this->hasMany(Fight::class);
    }

    /**
     * Relaciona enlaces de tickets asociados.
     */
    public function ticketLinks()
    {
        return $this->hasMany(TicketLink::class);
    }

    /**
     * Relaciona solicitudes de compra asociadas.
     */
    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class);
    }

    /**
     * Relaciona archivos multimedia asociados.
     */
    public function media()
    {
        return $this->hasMany(EventMedia::class);
    }

    /**
     * Relaciona sponsors asociados.
     */
    public function sponsors()
    {
        return $this->belongsToMany(Sponsor::class, 'event_sponsor')
            ->withPivot(['placement', 'display_order'])
            ->withTimestamps();
    }

    /**
     * Filtra registros visibles públicamente.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Devuelve la URL pública del póster.
     */
    public function posterUrl(): ?string
    {
        return PublicMedia::url($this->poster_image);
    }

    /**
     * Devuelve la URL pública del banner.
     */
    public function bannerUrl(): ?string
    {
        return PublicMedia::url($this->banner_image);
    }
}
