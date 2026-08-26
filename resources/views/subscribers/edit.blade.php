@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="bi bi-pencil"></i> Edit Subscriber</h2>
        <p class="text-muted">Update subscriber information</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('subscribers.update', $subscriber) }}">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Full Name *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $subscriber->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email', $subscriber->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                           id="phone" name="phone" value="{{ old('phone', $subscriber->phone) }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="customer_id" class="form-label">Customer ID *</label>
                    <input type="text" class="form-control @error('customer_id') is-invalid @enderror" 
                           id="customer_id" name="customer_id" value="{{ old('customer_id', $subscriber->customer_id) }}" required>
                    @error('customer_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" 
                              id="address" name="address" rows="2">{{ old('address', $subscriber->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="service_plan_id" class="form-label">Service Plan *</label>
                    <select class="form-select @error('service_plan_id') is-invalid @enderror" 
                            id="service_plan_id" name="service_plan_id" required>
                        <option value="">Select Plan</option>
                        @foreach($servicePlans as $plan)
                            <option value="{{ $plan->id }}" {{ old('service_plan_id', $subscriber->service_plan_id) == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} - ${{ $plan->price }}/{{ $plan->billing_cycle }}
                            </option>
                        @endforeach
                    </select>
                    @error('service_plan_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status *</label>
                    <select class="form-select @error('status') is-invalid @enderror" 
                            id="status" name="status" required>
                        <option value="active" {{ old('status', $subscriber->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="pending" {{ old('status', $subscriber->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="inactive" {{ old('status', $subscriber->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ old('status', $subscriber->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="cancelled" {{ old('status', $subscriber->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Update Subscriber</button>
                <a href="{{ route('subscribers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection