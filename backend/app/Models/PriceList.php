<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceList extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'file_path',
        'original_filename',
        'file_size',
        'mime_type',
        'items_count',
        'status',
        'processed_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'items_count' => 'integer',
        'processed_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSED = 'processed';
    const STATUS_FAILED = 'failed';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
