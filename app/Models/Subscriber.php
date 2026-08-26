<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'customer_id',
        'service_plan_id',
        'status',
        'preferences',
        'billing_info',
        'activated_at',
        'suspended_at',
        'cancelled_at',
        'data_usage',
        'is_priority'
    ];

    protected $casts = [
        'preferences' => 'array',
        'billing_info' => 'array',
        'activated_at' => 'datetime',
        'suspended_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'is_priority' => 'boolean'
    ];

    public function servicePlan(): BelongsTo
    {
        return $this->belongsTo(ServicePlan::class);
    }

    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'subscriber_device')
                    ->withPivot('status', 'assigned_at', 'deactivated_at', 'settings')
                    ->withTimestamps();
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByPlan($query, $planId)
    {
        return $query->where('service_plan_id', $planId);
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'suspended' => 'warning',
            'pending' => 'info',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    public function getFormattedDataUsageAttribute(): string
    {
        if ($this->data_usage < 1024) {
            return $this->data_usage . ' MB';
        }
        return round($this->data_usage / 1024, 2) . ' GB';
    }
}