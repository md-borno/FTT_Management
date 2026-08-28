@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="bi bi-pencil"></i> Edit Ticket</h2>
        <p class="text-muted">Update ticket information</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('tickets.update', $ticket) }}">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="title" class="form-label">Title *</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                           id="title" name="title" value="{{ old('title', $ticket->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Description *</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="4" required>{{ old('description', $ticket->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="priority" class="form-label">Priority *</label>
                    <select class="form-select @error('priority') is-invalid @enderror" 
                            id="priority" name="priority" required>
                        @foreach($priorities as $priority)
                            <option value="{{ $priority }}" {{ old('priority', $ticket->priority) == $priority ? 'selected' : '' }}>
                                {{ ucfirst($priority) }}
                            </option>
                        @endforeach
                    </select>
                    @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="category" class="form-label">Category *</label>
                    <select class="form-select @error('category') is-invalid @enderror" 
                            id="category" name="category" required>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ old('category', $ticket->category) == $category ? 'selected' : '' }}>
                                {{ ucfirst($category) }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="status" class="form-label">Status *</label>
                    <select class="form-select @error('status') is-invalid @enderror" 
                            id="status" name="status" required>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ old('status', $ticket->status) == $status ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="assigned_to" class="form-label">Assign To</label>
                    <select class="form-select @error('assigned_to') is-invalid @enderror" 
                            id="assigned_to" name="assigned_to">
                        <option value="">Unassigned</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to', $ticket->assigned_to) == $user->id ? 'selected' : '' }}>
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
                            <option value="{{ $subscriber->id }}" {{ old('subscriber_id', $ticket->subscriber_id) == $subscriber->id ? 'selected' : '' }}>
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
                            <option value="{{ $device->id }}" {{ old('device_id', $ticket->device_id) == $device->id ? 'selected' : '' }}>
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
                    <i class="bi bi-check-circle"></i> Update Ticket
                </button>
                <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection