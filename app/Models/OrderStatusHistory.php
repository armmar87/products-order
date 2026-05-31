<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class OrderStatusHistory extends Model
{
    protected $fillable = [
        'order_id',
        'status',
        'comment',
        'notify_customer',
        'updated_by',
    ];

    protected $casts = [
        'notify_customer' => 'boolean',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
