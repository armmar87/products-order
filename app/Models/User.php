<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * Get the user's profile.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the user's security settings.
     */
    public function security(): HasOne
    {
        return $this->hasOne(UserSecurity::class);
    }

    /**
     * Get the user's preferences.
     */
    public function preferences(): HasOne
    {
        return $this->hasOne(UserPreference::class);
    }

    /**
     * Get the user's payment information.
     */
    public function paymentInfo(): HasOne
    {
        return $this->hasOne(UserPaymentInfo::class);
    }

    public function isCustomer(): bool
    {
        return $this->role === UserRole::Customer;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }
}
