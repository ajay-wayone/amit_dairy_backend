<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserOtp extends Model
{
    // If you have a timestamps in your table, leave this as is
    // Otherwise, if no created_at, updated_at columns, disable timestamps:
    // public $timestamps = false;

    protected $fillable = [
        'user_id',
        'otp',
        'is_used',
        'expires_at',
    ];

    // Cast expires_at to datetime automatically
    protected $casts = [
        'expires_at' => 'datetime',
        'is_used'    => 'boolean',
    ];

    /**
     * Relationship to User model
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if OTP is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Mark OTP as used
     */
    public function markAsUsed(): void
    {
        $this->is_used = true;
        $this->save();
    }
}
