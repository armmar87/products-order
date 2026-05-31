<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'coupon_id',
        'status',
        'subtotal',
        'tax',
        'discount',
        'shipping_cost',
        'total',
        'currency',
        'notes',
        'ip_address',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'status' => OrderStatus::class,
        'discount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(OrderAddress::class);
    }

    public function shippingAddress(): HasOne
    {
        return $this->hasOne(OrderAddress::class)->where('type', 'shipping');
    }


    public function billingAddress(): HasOne
    {
        return $this->hasOne(OrderAddress::class)->where('type', 'billing');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(OrderPayment::class);
    }

    public function fulfillment(): HasOne
    {
        return $this->hasOne(OrderFulfillment::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }


    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
