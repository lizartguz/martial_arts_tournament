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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function coverImageUrl(): ?string
    {
        return $this->cover_image ? asset($this->cover_image) : null;
    }

    public function scopePublished($query)
    {
        return $query
            ->where('status', 1)
            ->where(fn ($subQuery) => $subQuery
                ->whereNull('published_at')
                ->orWhere('published_at', '<=', now()));
    }

    public function isPublished(): bool
    {
        return (int) $this->status === 1
            && (! $this->published_at || $this->published_at->lte(now()));
    }
}
