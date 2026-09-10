<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $primaryKey = 'notification_id';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'scheduled_for',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'scheduled_for' => 'datetime',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    public function scopeDue(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('scheduled_for')
                ->orWhere('scheduled_for', '<=', now());
        });
    }

    public function markAsRead(): bool
    {
        if ($this->is_read) {
            return true;
        }

        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function markAsUnread(): bool
    {
        if (! $this->is_read) {
            return true;
        }

        return $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }
}
