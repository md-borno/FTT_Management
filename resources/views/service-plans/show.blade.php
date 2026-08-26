<!-- resources/views/service-plans/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="bi bi-tag"></i> Service Plan Details</h2>
        <p class="text-muted">View service plan information</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('service-plans.edit', $servicePlan) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('service-plans.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">{{ $servicePlan->name }}</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Slug:</strong> {{ $servicePlan->slug }}</p>
                        <p><strong>Bandwidth:</strong> {{ $servicePlan->bandwidth ?? 'N/A' }}</p>
                        <p><strong>Price:</strong> ${{ number_format($servicePlan->price, 2) }}</p>
                        <p><strong>Billing Cycle:</strong> {{ ucfirst($servicePlan->billing_cycle) }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> 
                            <span class="badge bg-{{ $servicePlan->is_active ? 'success' : 'danger' }}">
                                {{ $servicePlan->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                        <p><strong>Sort Order:</strong> {{ $servicePlan->sort_order }}</p>
                        <p><strong>Created:</strong> {{ $servicePlan->created_at->format('Y-m-d H:i:s') }}</p>
                        <p><strong>Updated:</strong> {{ $servicePlan->updated_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                </div>
                
                @if($servicePlan->description)
                <hr>
                <h6 class="text-muted">Description</h6>
                <p>{{ $servicePlan->description }}</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Features</h6>
            </div>
            <div class="card-body">
                @php
                    $features = is_array($servicePlan->features) ? $servicePlan->features : json_decode($servicePlan->features ?? '[]', true);
                    if (!is_array($features)) $features = [];
                @endphp
                @if(count($features) > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($features as $feature)
                            <li class="list-group-item">
                                <i class="bi bi-check-circle text-success"></i> 
                                {{ str_replace('_', ' ', ucfirst($feature)) }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">No features defined.</p>
                @endif
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Limits</h6>
            </div>
            <div class="card-body">
                @php
                    $limits = is_array($servicePlan->limits) ? $servicePlan->limits : json_decode($servicePlan->limits ?? '[]', true);
                    if (!is_array($limits)) $limits = [];
                @endphp
                @if(count($limits) > 0)
                    @foreach($limits as $key => $value)
                        <p><strong>{{ str_replace('_', ' ', ucfirst($key)) }}:</strong> {{ $value }}</p>
                    @endforeach
                @else
                    <p class="text-muted">No limits defined.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection