<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'company_name',
        'is_active',
        'is_blocked',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_blocked' => 'boolean',
    ];

    const ROLE_GUEST = 'guest';
    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    public function isGuest(): bool
    {
        return $this->role === self::ROLE_GUEST;
    }

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function components(): HasMany
    {
        return $this->hasMany(Component::class, 'supplier_id');
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function priceLists(): HasMany
    {
        return $this->hasMany(PriceList::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
