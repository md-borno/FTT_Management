<!-- resources/views/alarms/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="bi bi-plus-circle"></i> Create Alarm</h2>
        <p class="text-muted">Create a new network alarm</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('alarms.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('alarms.store') }}">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Alarm Name *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name') }}" required>
                    <small class="text-muted">e.g., "High Packet Loss" or "Device Unreachable"</small>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="severity" class="form-label">Severity *</label>
                    <select class="form-select @error('severity') is-invalid @enderror" 
                            id="severity" name="severity" required>
                        <option value="">Select Severity</option>
                        <option value="critical" {{ old('severity') == 'critical' ? 'selected' : '' }}>Critical 🔴</option>
                        <option value="major" {{ old('severity') == 'major' ? 'selected' : '' }}>Major 🟠</option>
                        <option value="minor" {{ old('severity') == 'minor' ? 'selected' : '' }}>Minor 🟡</option>
                        <option value="warning" {{ old('severity') == 'warning' ? 'selected' : '' }}>Warning 🟣</option>
                    </select>
                    @error('severity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="source" class="form-label">Source *</label>
                    <input type="text" class="form-control @error('source') is-invalid @enderror" 
                           id="source" name="source" value="{{ old('source', 'System') }}" required>
                    <small class="text-muted">e.g., "System", "Monitoring", "OLT", "ONT"</small>
                    @error('source')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="device_id" class="form-label">Device *</label>
                    <select class="form-select @error('device_id') is-invalid @enderror" 
                            id="device_id" name="device_id" required>
                        <option value="">Select Device</option>
                        @foreach($devices as $device)
                            <option value="{{ $device->id }}" {{ old('device_id') == $device->id ? 'selected' : '' }}>
                                {{ $device->name }} ({{ $device->status }})
                            </option>
                        @endforeach
                    </select>
                    @error('device_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Description *</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                    <small class="text-muted">Detailed description of the alarm issue</small>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="occurred_at" class="form-label">Occurred At *</label>
                    <input type="datetime-local" class="form-control @error('occurred_at') is-invalid @enderror" 
                           id="occurred_at" name="occurred_at" value="{{ old('occurred_at', now()->format('Y-m-d\TH:i')) }}" required>
                    @error('occurred_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Create Alarm
                </button>
                <a href="{{ route('alarms.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection