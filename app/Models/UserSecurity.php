<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSecurity extends Model
{
    protected $fillable = [
        'user_id',
        'api_token',
        'two_factor_secret',
        'login_attempts',
        'last_login_at',
        'is_banned',
        'ban_reason',
        'banned_at',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
        'is_banned' => 'boolean',
        'banned_at' => 'datetime',
        'login_attempts' => 'integer',
    ];

    protected $hidden = [
        'api_token',
        'two_factor_secret',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}


