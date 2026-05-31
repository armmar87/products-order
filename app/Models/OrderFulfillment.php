<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class OrderFulfillment extends Model
{
    protected $fillable = [
        'order_id',
        'tracking_number',
        'carrier',
        'shipping_method',
        'fulfillment_status',
        'shipped_at',
        'delivered_at',
        'expected_delivery_at',
        'notes',
    ];
    protected $casts = [
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'expected_delivery_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
