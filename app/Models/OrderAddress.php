<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class OrderAddress extends Model
{
    protected $fillable = [
        'order_id',
        'type',
        'first_name',
        'last_name',
        'email',
        'phone',
        'company',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
    ];
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line1,
            $this->address_line2,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);
        return implode(', ', $parts);
    }
    public function isShipping(): bool
    {
        return $this->type === 'shipping';
    }
    public function isBilling(): bool
    {
        return $this->type === 'billing';
    }
}
