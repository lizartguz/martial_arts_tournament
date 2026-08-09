<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountDeletionRequestM extends Model
{
    use HasFactory;
   
    const STATE_PENDING = 0;
    const STATE_APPROVED = 1;
    const STATE_REJECTED = 2;
    protected $table = 'account_deletion_requests';
    protected $fillable = [
        'user_id',
        'reason',
        'state',
        'validator_user_id',
        'validation_comment'
    ];

    protected $casts = [
        'state' => 'integer', // Explicit cast for the state
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationship: User who requested deletion
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship: Admin/validator who processed the request
    public function validator()
    {
        return $this->belongsTo(User::class, 'validator_user_id');
    }

    // Scopes for easy querying
    public function scopePending($query)
    {
        return $query->where('state', self::STATE_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('state', self::STATE_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('state', self::STATE_REJECTED);
    }

    // Helper methods to check state
    public function isPending()
    {
        return $this->state === self::STATE_PENDING;
    }

    public function isApproved()
    {
        return $this->state === self::STATE_APPROVED;
    }

    public function isRejected()
    {
        return $this->state === self::STATE_REJECTED;
    }
}
