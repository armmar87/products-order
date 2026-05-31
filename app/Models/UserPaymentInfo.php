<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'stripe_customer_id', 'payment_method'])]
#[Hidden(['stripe_customer_id'])]
class UserPaymentInfo extends Model
{
    protected $table = 'user_payment_info';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

