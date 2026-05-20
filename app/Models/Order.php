<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PAID = 'paid';
    const STATUS_PROCESSING = 'processing';
    const STATUS_PACKED = 'packed';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    const ALL_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_PAID,
        self::STATUS_PROCESSING,
        self::STATUS_PACKED,
        self::STATUS_SHIPPED,
        self::STATUS_IN_TRANSIT,
        self::STATUS_DELIVERED,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
        self::STATUS_REFUNDED,
    ];

    protected $fillable = [
        'buyer_id', 'business_id', 'type', 'status',
        'total', 'commission', 'platform_fee',
        'shipping_address', 'billing_address', 'shipping_fee',
        'subtotal', 'discount_total', 'tax',
        'tracking_number', 'notes', 'estimated_delivery_date',
        'payment_status', 'paid_at', 'shipped_at',
        'delivered_at', 'cancelled_at'
    ];

    protected function casts(): array
    {
        return [
            'shipping_address' => 'array',
            'billing_address' => 'array',
            'paid_at' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function business()
    {
        return $this->belongsTo(User::class, 'business_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function disputes()
    {
        return $this->hasMany(Dispute::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public static function validTransitions(): array
    {
        return [
            self::STATUS_PENDING => [self::STATUS_CONFIRMED, self::STATUS_CANCELLED],
            self::STATUS_CONFIRMED => [self::STATUS_PAID, self::STATUS_CANCELLED],
            self::STATUS_PAID => [self::STATUS_PROCESSING, self::STATUS_REFUNDED],
            self::STATUS_PROCESSING => [self::STATUS_PACKED, self::STATUS_REFUNDED],
            self::STATUS_PACKED => [self::STATUS_SHIPPED, self::STATUS_REFUNDED],
            self::STATUS_SHIPPED => [self::STATUS_IN_TRANSIT, self::STATUS_DELIVERED, self::STATUS_REFUNDED],
            self::STATUS_IN_TRANSIT => [self::STATUS_DELIVERED, self::STATUS_REFUNDED],
            self::STATUS_DELIVERED => [self::STATUS_COMPLETED, self::STATUS_REFUNDED],
            self::STATUS_COMPLETED => [self::STATUS_REFUNDED],
            self::STATUS_CANCELLED => [],
            self::STATUS_REFUNDED => [],
        ];
    }

    public function canTransitionTo(string $newStatus): bool
    {
        $valid = self::validTransitions()[$this->status] ?? [];
        return in_array($newStatus, $valid, true);
    }

    public function transitionTo(string $newStatus, ?string $reason = null): bool
    {
        if (!$this->canTransitionTo($newStatus)) {
            return false;
        }

        $this->status = $newStatus;

        match ($newStatus) {
            self::STATUS_PAID => $this->paid_at = now(),
            self::STATUS_SHIPPED => $this->shipped_at = now(),
            self::STATUS_DELIVERED => $this->delivered_at = now(),
            self::STATUS_CANCELLED => $this->cancelled_at = now(),
            default => null,
        };

        $this->save();

        return true;
    }

    public function scopeRetail($query)
    {
        return $query->where('type', 'retail');
    }

    public function scopeB2b($query)
    {
        return $query->where('type', 'b2b');
    }

    public function isPaid(): bool
    {
        return in_array($this->status, [self::STATUS_PAID, self::STATUS_PROCESSING, self::STATUS_PACKED, self::STATUS_SHIPPED, self::STATUS_IN_TRANSIT, self::STATUS_DELIVERED, self::STATUS_COMPLETED], true);
    }

    public function isFulfilled(): bool
    {
        return in_array($this->status, [self::STATUS_DELIVERED, self::STATUS_COMPLETED], true);
    }
}
