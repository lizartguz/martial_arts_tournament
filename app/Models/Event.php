<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'starts_at' => 'datetime',
        'doors_open_at' => 'datetime',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'status' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function fights()
    {
        return $this->hasMany(Fight::class);
    }

    public function ticketLinks()
    {
        return $this->hasMany(TicketLink::class);
    }

    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class);
    }

    public function media()
    {
        return $this->hasMany(EventMedia::class);
    }

    public function sponsors()
    {
        return $this->belongsToMany(Sponsor::class, 'event_sponsor')
            ->withPivot(['placement', 'display_order'])
            ->withTimestamps();
    }

    public function scopePublished($query)
    {
        return $query->where('status', 1);
    }

    public function posterUrl(): ?string
    {
        return $this->poster_image ? asset($this->poster_image) : null;
    }

    public function bannerUrl(): ?string
    {
        return $this->banner_image ? asset($this->banner_image) : null;
    }
}
