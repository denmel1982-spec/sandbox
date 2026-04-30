<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'component_id',
        'user_id',
        'contact_email',
        'contact_phone',
        'price',
        'currency',
        'quantity',
        'status',
        'published_at',
        'expires_at',
        'views_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'views_count' => 'integer',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    const TYPE_BUY = 'buy';
    const TYPE_SELL = 'sell';

    const STATUS_ACTIVE = 'active';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_MODERATED = 'moderated';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }

    public function scopeSearch($query, ?string $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('title', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%");
            });
        }
        return $query;
    }

    public function scopeDateFilter($query, ?string $dateFilter)
    {
        if ($dateFilter === 'today') {
            return $query->whereDate('published_at', today());
        }
        if ($dateFilter === 'yesterday') {
            return $query->whereDate('published_at', today()->subDay());
        }
        return $query;
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }
}
