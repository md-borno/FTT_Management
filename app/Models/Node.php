<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Node extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'device_id',
        'location_id',
        'x_position',
        'y_position',
        'properties',
        'is_active'
    ];

    protected $casts = [
        'properties' => 'array',
        'x_position' => 'float',
        'y_position' => 'float',
        'is_active' => 'boolean'
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function sourceLinks(): HasMany
    {
        return $this->hasMany(Link::class, 'source_node_id');
    }

    public function targetLinks(): HasMany
    {
        return $this->hasMany(Link::class, 'target_node_id');
    }

    public function getConnectedNodes()
    {
        $sourceIds = $this->sourceLinks->pluck('target_node_id')->toArray();
        $targetIds = $this->targetLinks->pluck('source_node_id')->toArray();
        $nodeIds = array_merge($sourceIds, $targetIds);
        
        return Node::whereIn('id', $nodeIds)->get();
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->device->status ?? 'offline') {
            'online' => 'success',
            'offline' => 'danger',
            'maintenance' => 'warning',
            default => 'secondary'
        };
    }
}