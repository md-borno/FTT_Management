@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="bi bi-pencil"></i> Edit Device</h2>
        <p class="text-muted">Update device information</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('devices.update', $device) }}">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Device Name *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $device->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="serial_number" class="form-label">Serial Number *</label>
                    <input type="text" class="form-control @error('serial_number') is-invalid @enderror" 
                           id="serial_number" name="serial_number" value="{{ old('serial_number', $device->serial_number) }}" required>
                    @error('serial_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="ip_address" class="form-label">IP Address</label>
                    <input type="text" class="form-control @error('ip_address') is-invalid @enderror" 
                           id="ip_address" name="ip_address" value="{{ old('ip_address', $device->ip_address) }}">
                    @error('ip_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="mac_address" class="form-label">MAC Address</label>
                    <input type="text" class="form-control @error('mac_address') is-invalid @enderror" 
                           id="mac_address" name="mac_address" value="{{ old('mac_address', $device->mac_address) }}">
                    @error('mac_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="device_type_id" class="form-label">Device Type *</label>
                    <select class="form-select @error('device_type_id') is-invalid @enderror" 
                            id="device_type_id" name="device_type_id" required>
                        <option value="">Select Type</option>
                        @foreach($deviceTypes as $type)
                            <option value="{{ $type->id }}" {{ old('device_type_id', $device->device_type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('device_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="location_id" class="form-label">Location</label>
                    <select class="form-select @error('location_id') is-invalid @enderror" 
                            id="location_id" name="location_id">
                        <option value="">Select Location</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ old('location_id', $device->location_id) == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('location_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status *</label>
                    <select class="form-select @error('status') is-invalid @enderror" 
                            id="status" name="status" required>
                        <option value="online" {{ old('status', $device->status) == 'online' ? 'selected' : '' }}>Online</option>
                        <option value="offline" {{ old('status', $device->status) == 'offline' ? 'selected' : '' }}>Offline</option>
                        <option value="maintenance" {{ old('status', $device->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="decommissioned" {{ old('status', $device->status) == 'decommissioned' ? 'selected' : '' }}>Decommissioned</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Update Device</button>
                <a href="{{ route('devices.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection