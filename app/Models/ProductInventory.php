<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductInventory extends Model
{
    protected $table = 'product_inventory';

    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'stock',
        'min_stock',
        'reserved_stock',
    ];

    protected $casts = [
        'stock' => 'integer',
        'min_stock' => 'integer',
        'reserved_stock' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

