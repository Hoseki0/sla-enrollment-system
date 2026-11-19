<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'admin_id', 'title', 'body', 'audience', 'priority',
        'is_published', 'published_at', 'expires_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    public function scopeForAudience($query, string $levelType)
    {
        return $query->where(fn ($q) =>
            $q->where('audience', 'all')
              ->orWhere('audience', strtolower($levelType))
        );
    }

    public function scopeOrderByPriority($query)
    {
        return $query->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'important' THEN 2 ELSE 3 END ASC");
    }

    public function getPriorityBadgeAttribute(): string
    {
        return match ($this->priority) {
            'urgent'    => 'danger',
            'important' => 'warning',
            default     => 'info',
        };
    }

    public function getPriorityIconAttribute(): string
    {
        return match ($this->priority) {
            'urgent'    => 'bi-exclamation-triangle-fill',
            'important' => 'bi-info-circle-fill',
            default     => 'bi-megaphone-fill',
        };
    }
}
