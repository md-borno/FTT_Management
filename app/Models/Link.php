<?php
// app/Models/Link.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_node_id',
        'target_node_id',
        'type',
        'status',
        'distance',
        'capacity',
        'properties'
    ];

    protected $casts = [
        'properties' => 'array',
        'distance' => 'float',
        'capacity' => 'float'
    ];

    public function sourceNode(): BelongsTo
    {
        return $this->belongsTo(Node::class, 'source_node_id');
    }

    public function targetNode(): BelongsTo
    {
        return $this->belongsTo(Node::class, 'target_node_id');
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'danger',
            'maintenance' => 'warning',
            default => 'secondary'
        };
    }
}