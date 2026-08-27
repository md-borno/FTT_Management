<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'serial_number',
        'ip_address',
        'mac_address',
        'firmware_version',
        'model',
        'manufacturer',
        'device_type_id',
        'location_id',
        'parent_device_id',
        'status',
        'configuration',
        'capabilities',
        'metadata',
        'last_seen_at',
        'installed_at',
        'warranty_expiry',
        'is_monitored'
    ];

    protected $casts = [
        'configuration' => 'array',
        'capabilities' => 'array',
        'metadata' => 'array',
        'last_seen_at' => 'datetime',
        'installed_at' => 'datetime',
        'warranty_expiry' => 'datetime',
        'is_monitored' => 'boolean'
    ];

    public function deviceType(): BelongsTo
    {
        return $this->belongsTo(DeviceType::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function parentDevice(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'parent_device_id');
    }

    public function childDevices(): HasMany
    {
        return $this->hasMany(Device::class, 'parent_device_id');
    }

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(Subscriber::class, 'subscriber_device')
                    ->withPivot('status', 'assigned_at', 'deactivated_at', 'settings')
                    ->withTimestamps();
    }

    public function alarms(): HasMany
    {
        return $this->hasMany(Alarm::class);
    }

    public function performanceMetrics(): HasMany
    {
        return $this->hasMany(PerformanceMetric::class);
    }

    public function maintenanceSchedules(): HasMany
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    // মেথডের নাম বহুবচনে `nodes` করা হয়েছে
    public function nodes(): HasMany
    {
        return $this->hasMany(Node::class);
    }

    // Scopes
    public function scopeOnline($query)
    {
        return $query->where('status', 'online');
    }

    public function scopeByType($query, $type)
    {
        return $query->whereHas('deviceType', function($q) use ($type) {
            $q->where('slug', $type);
        });
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'online' => 'success',
            'offline' => 'danger',
            'maintenance' => 'warning',
            'decommissioned' => 'secondary',
            default => 'secondary'
        };
    }

    public function getUptimeAttribute(): string
    {
        if (!$this->last_seen_at) {
            return 'Unknown';
        }
        
        $diff = now()->diff($this->last_seen_at);
        if ($diff->days > 0) {
            return $diff->days . 'd ' . $diff->h . 'h';
        }
        return $diff->h . 'h ' . $diff->i . 'm';
    }

    public function node()
{
    return $this->hasOne(Node::class);
}

// all nodes connected to this device's node
public function connectedNodes()
{
    $node = $this->node;
    if (!$node) {
        return collect();
    }
    return $node->getConnectedNodes();
}
}