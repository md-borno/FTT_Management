@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="bi bi-hdd-stack"></i> Device Details</h2>
        <p class="text-muted">View device information</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('devices.edit', $device) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('devices.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted">Device Information</h6>
                <hr>
                <p><strong>Name:</strong> {{ $device->name }}</p>
                <p><strong>Serial Number:</strong> {{ $device->serial_number }}</p>
                <p><strong>IP Address:</strong> {{ $device->ip_address ?? 'N/A' }}</p>
                <p><strong>MAC Address:</strong> {{ $device->mac_address ?? 'N/A' }}</p>
                <p><strong>Status:</strong> 
                    <span class="status-badge status-{{ $device->status }}">
                        {{ ucfirst($device->status) }}
                    </span>
                </p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted">Technical Details</h6>
                <hr>
                <p><strong>Device Type:</strong> {{ $device->deviceType->name ?? 'N/A' }}</p>
                <p><strong>Location:</strong> {{ $device->location->name ?? 'N/A' }}</p>
                <p><strong>Firmware:</strong> {{ $device->firmware_version ?? 'N/A' }}</p>
                <p><strong>Model:</strong> {{ $device->model ?? 'N/A' }}</p>
                <p><strong>Manufacturer:</strong> {{ $device->manufacturer ?? 'N/A' }}</p>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <h6 class="text-muted">Installation Details</h6>
                <hr>
                <p><strong>Installed:</strong> {{ $device->installed_at ? $device->installed_at->format('Y-m-d') : 'N/A' }}</p>
                <p><strong>Last Seen:</strong> {{ $device->last_seen_at ? $device->last_seen_at->diffForHumans() : 'Never' }}</p>
                <p><strong>Warranty:</strong> {{ $device->warranty_expiry ? $device->warranty_expiry->format('Y-m-d') : 'N/A' }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted">Subscribers</h6>
                <hr>
                @if($device->subscribers->count() > 0)
                    <ul class="list-group">
                        @foreach($device->subscribers as $subscriber)
                            <li class="list-group-item">
                                {{ $subscriber->name }}
                                <span class="badge bg-{{ $subscriber->status === 'active' ? 'success' : 'secondary' }} float-end">
                                    {{ ucfirst($subscriber->status) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">No subscribers assigned.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection