<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    protected $table = 'user_subscriptions';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'user_id',
        'razorpay_subscription_id',
        'razorpay_payment_id',
        'upfront_amount',
        'monthly_amount',
        'status',
        'razorpay_current_start',
        'razorpay_current_end',
        'ends_at',
    ];

    /**
     * Cast attributes
     */
    protected $casts = [
        'upfront_amount' => 'decimal:2',
        'monthly_amount' => 'decimal:2',
        'razorpay_current_start' => 'datetime',
        'razorpay_current_end' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Relationship: Subscription belongs to a User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helpers (optional but useful)
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }
}
