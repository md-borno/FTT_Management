<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alarm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'severity',
        'source',
        'device_id',
        'description',
        'resolution',
        'parameters',
        'metadata',
        'occurred_at',
        'acknowledged_at',
        'resolved_at',
        'acknowledged_by',
        'resolved_by',
        'is_auto_resolved'
    ];

    protected $casts = [
        'parameters' => 'array',
        'metadata' => 'array',
        'occurred_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'resolved_at' => 'datetime',
        'is_auto_resolved' => 'boolean'
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNull('resolved_at');
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    // Accessors
    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'critical' => 'danger',
            'major' => 'warning',
            'minor' => 'info',
            'warning' => 'secondary',
            default => 'secondary'
        };
    }

    public function getIsActiveAttribute(): bool
    {
        return is_null($this->resolved_at);
    }

    public function getAgeAttribute(): string
    {
        if (!$this->occurred_at) {
            return 'Unknown';
        }
        
        $diff = now()->diff($this->occurred_at);
        if ($diff->days > 0) {
            return $diff->days . 'd ' . $diff->h . 'h';
        }
        if ($diff->h > 0) {
            return $diff->h . 'h ' . $diff->i . 'm';
        }
        return $diff->i . 'm';
    }
}