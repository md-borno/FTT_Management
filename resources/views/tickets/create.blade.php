@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="bi bi-plus-circle"></i> Create Ticket</h2>
        <p class="text-muted">Create a new support ticket</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('tickets.store') }}">
            @csrf
            
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="title" class="form-label">Title *</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                           id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Description *</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="priority" class="form-label">Priority *</label>
                    <select class="form-select @error('priority') is-invalid @enderror" 
                            id="priority" name="priority" required>
                        <option value="">Select Priority</option>
                        @foreach($priorities as $priority)
                            <option value="{{ $priority }}" {{ old('priority') == $priority ? 'selected' : '' }}>
                                {{ ucfirst($priority) }}
                            </option>
                        @endforeach
                    </select>
                    @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="category" class="form-label">Category *</label>
                    <select class="form-select @error('category') is-invalid @enderror" 
                            id="category" name="category" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                                {{ ucfirst($category) }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="assigned_to" class="form-label">Assign To</label>
                    <select class="form-select @error('assigned_to') is-invalid @enderror" 
                            id="assigned_to" name="assigned_to">
                        <option value="">Unassigned</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="subscriber_id" class="form-label">Subscriber</label>
                    <select class="form-select @error('subscriber_id') is-invalid @enderror" 
                            id="subscriber_id" name="subscriber_id">
                        <option value="">Select Subscriber</option>
                        @foreach($subscribers as $subscriber)
                            <option value="{{ $subscriber->id }}" {{ old('subscriber_id') == $subscriber->id ? 'selected' : '' }}>
                                {{ $subscriber->name }} ({{ $subscriber->customer_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('subscriber_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="device_id" class="form-label">Device</label>
                    <select class="form-select @error('device_id') is-invalid @enderror" 
                            id="device_id" name="device_id">
                        <option value="">Select Device</option>
                        @foreach($devices as $device)
                            <option value="{{ $device->id }}" {{ old('device_id') == $device->id ? 'selected' : '' }}>
                                {{ $device->name }} ({{ $device->serial_number }})
                            </option>
                        @endforeach
                    </select>
                    @error('device_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Create Ticket
                </button>
                <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection