<!-- resources/views/alarms/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="bi bi-bell"></i> Alarm Details</h2>
        <p class="text-muted">View alarm information</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('alarms.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Alarms
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Alarm Details Card -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <span class="badge bg-{{ $alarm->severity_color }} me-2">
                        {{ strtoupper($alarm->severity) }}
                    </span>
                    {{ $alarm->name }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Device:</strong> 
                            <a href="{{ route('devices.show', $alarm->device_id) }}">
                                {{ $alarm->device->name ?? 'N/A' }}
                            </a>
                        </p>
                        <p><strong>Source:</strong> {{ $alarm->source }}</p>
                        <p><strong>Occurred At:</strong> {{ $alarm->occurred_at->format('Y-m-d H:i:s') }}</p>
                        <p><strong>Age:</strong> {{ $alarm->age }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong>
                            @if($alarm->resolved_at)
                                <span class="badge bg-success">Resolved</span>
                            @elseif($alarm->acknowledged_at)
                                <span class="badge bg-warning">Acknowledged</span>
                            @else
                                <span class="badge bg-danger">Active</span>
                            @endif
                        </p>
                        @if($alarm->acknowledged_at)
                            <p><strong>Acknowledged At:</strong> {{ $alarm->acknowledged_at->format('Y-m-d H:i:s') }}</p>
                            <p><strong>Acknowledged By:</strong> {{ $alarm->acknowledgedBy->name ?? 'N/A' }}</p>
                        @endif
                        @if($alarm->resolved_at)
                            <p><strong>Resolved At:</strong> {{ $alarm->resolved_at->format('Y-m-d H:i:s') }}</p>
                            <p><strong>Resolved By:</strong> {{ $alarm->resolvedBy->name ?? 'N/A' }}</p>
                            <p><strong>Resolution:</strong> {{ $alarm->resolution ?? 'N/A' }}</p>
                        @endif
                        @if($alarm->is_auto_resolved)
                            <p><span class="badge bg-info">Auto-Resolved</span></p>
                        @endif
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-muted">Description</h6>
                        <p>{{ $alarm->description }}</p>
                    </div>
                </div>
                
                @if($alarm->parameters)
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="text-muted">Parameters</h6>
                        <pre class="bg-light p-3 rounded">{{ json_encode($alarm->parameters, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Actions Card -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Actions</h6>
            </div>
            <div class="card-body">
                @if(!$alarm->resolved_at)
                    @if(!$alarm->acknowledged_at)
                        <form action="{{ route('alarms.acknowledge', $alarm) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100 mb-2">
                                <i class="bi bi-check-circle"></i> Acknowledge Alarm
                            </button>
                        </form>
                    @endif
                    
                    <form action="{{ route('alarms.resolve', $alarm) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label for="resolution" class="form-label">Resolution Notes:</label>
                            <textarea name="resolution" class="form-control" rows="3" 
                                      placeholder="Describe how you resolved this issue..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check2-circle"></i> Resolve Alarm
                        </button>
                    </form>
                @else
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> This alarm has been resolved.
                    </div>
                    <p class="text-muted small">
                        Resolved: {{ $alarm->resolved_at->diffForHumans() }}
                    </p>
                @endif
            </div>
        </div>
        
        <!-- Device Info Card -->
        @if($alarm->device)
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-hdd-stack"></i> Device Information</h6>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $alarm->device->name }}</p>
                <p><strong>Type:</strong> {{ $alarm->device->deviceType->name ?? 'N/A' }}</p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-{{ $alarm->device->status_color }}">
                        {{ ucfirst($alarm->device->status) }}
                    </span>
                </p>
                <p><strong>IP Address:</strong> {{ $alarm->device->ip_address ?? 'N/A' }}</p>
                <a href="{{ route('devices.show', $alarm->device) }}" class="btn btn-sm btn-outline-primary w-100">
                    <i class="bi bi-eye"></i> View Device
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection