<?php
// app/Models/ServicePlan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'bandwidth',
        'price',
        'billing_cycle',
        'features',
        'limits',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'features' => 'array',
        'limits' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function subscribers(): HasMany
    {
        return $this->hasMany(Subscriber::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    public function getFeaturesListAttribute(): string
    {
        if (empty($this->features)) {
            return 'None';
        }
        return implode(', ', array_map(function($feature) {
            return str_replace('_', ' ', ucfirst($feature));
        }, $this->features));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}