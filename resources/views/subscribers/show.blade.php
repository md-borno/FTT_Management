@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="bi bi-person"></i> Subscriber Details</h2>
        <p class="text-muted">View subscriber information</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('subscribers.edit', $subscriber) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('subscribers.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted">Personal Information</h6>
                <hr>
                <p><strong>Name:</strong> {{ $subscriber->name }}</p>
                <p><strong>Email:</strong> {{ $subscriber->email }}</p>
                <p><strong>Phone:</strong> {{ $subscriber->phone ?? 'N/A' }}</p>
                <p><strong>Address:</strong> {{ $subscriber->address ?? 'N/A' }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted">Account Information</h6>
                <hr>
                <p><strong>Customer ID:</strong> {{ $subscriber->customer_id }}</p>
                <p><strong>Service Plan:</strong> {{ $subscriber->servicePlan->name ?? 'N/A' }}</p>
                <p><strong>Bandwidth:</strong> {{ $subscriber->servicePlan->bandwidth ?? 'N/A' }}</p>
                <p><strong>Status:</strong> 
                    <span class="status-badge status-{{ $subscriber->status }}">
                        {{ ucfirst($subscriber->status) }}
                    </span>
                </p>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <h6 class="text-muted">Usage Information</h6>
                <hr>
                <p><strong>Data Usage:</strong> {{ $subscriber->formatted_data_usage ?? '0 MB' }}</p>
                <p><strong>Priority:</strong> {{ $subscriber->is_priority ? 'Yes' : 'No' }}</p>
                <p><strong>Activated:</strong> {{ $subscriber->activated_at ? $subscriber->activated_at->format('Y-m-d') : 'Not activated' }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted">Devices</h6>
                <hr>
                @if($subscriber->devices->count() > 0)
                    <ul class="list-group">
                        @foreach($subscriber->devices as $device)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $device->name }}
                                <span class="badge bg-{{ $device->status === 'online' ? 'success' : 'danger' }}">
                                    {{ ucfirst($device->status) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">No devices assigned.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection