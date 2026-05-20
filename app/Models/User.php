<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    const ROLE_ADMIN = 'admin';
    const ROLE_BUSINESS = 'business';
    const ROLE_BUYER = 'buyer';

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_ACTIVE = 'active';
    const STATUS_REJECTED = 'rejected';
    const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'verification_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isBusiness(): bool
    {
        return $this->role === self::ROLE_BUSINESS;
    }

    public function isBuyer(): bool
    {
        return $this->role === self::ROLE_BUYER;
    }

    public function businessProfile()
    {
        return $this->hasOne(\App\Models\BusinessProfile::class);
    }

    public function ordersAsBusiness()
    {
        return $this->hasMany(\App\Models\Order::class, 'business_id');
    }

    public function ordersAsBuyer()
    {
        return $this->hasMany(\App\Models\Order::class, 'buyer_id');
    }

    public function cart()
    {
        return $this->hasOne(\App\Models\Cart::class);
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(\App\Models\Wishlist::class);
    }

    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    public function transactionLogs()
    {
        return $this->hasMany(\App\Models\TransactionLog::class);
    }

    public function shippingRules()
    {
        return $this->hasMany(\App\Models\ShippingRule::class, 'business_id');
    }

    public function discountTiers()
    {
        return $this->hasMany(\App\Models\DiscountTier::class, 'business_id');
    }

    public function preorderQueues()
    {
        return $this->hasMany(\App\Models\PreorderQueue::class);
    }
}
