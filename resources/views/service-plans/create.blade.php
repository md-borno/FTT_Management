@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="bi bi-plus-circle"></i> Create Service Plan</h2>
        <p class="text-muted">Create a new service plan for subscribers</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('service-plans.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('service-plans.store') }}">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Plan Name *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="slug" class="form-label">Slug *</label>
                    <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                           id="slug" name="slug" value="{{ old('slug') }}" required>
                    <small class="text-muted">Unique identifier (e.g., basic, premium)</small>
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="bandwidth" class="form-label">Bandwidth</label>
                    <input type="text" class="form-control @error('bandwidth') is-invalid @enderror" 
                           id="bandwidth" name="bandwidth" value="{{ old('bandwidth', '100 Mbps') }}" 
                           placeholder="e.g., 100 Mbps, 1 Gbps">
                    @error('bandwidth')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="price" class="form-label">Price *</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                               id="price" name="price" value="{{ old('price', 49.99) }}" required>
                    </div>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="billing_cycle" class="form-label">Billing Cycle *</label>
                    <select class="form-select @error('billing_cycle') is-invalid @enderror" 
                            id="billing_cycle" name="billing_cycle" required>
                        <option value="monthly" {{ old('billing_cycle') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="quarterly" {{ old('billing_cycle') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                        <option value="yearly" {{ old('billing_cycle') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                    @error('billing_cycle')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="sort_order" class="form-label">Sort Order</label>
                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                           id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                    <small class="text-muted">Lower numbers appear first</small>
                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="2">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Features</label>
                    <div class="border rounded p-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="features[]" value="internet" 
                                   {{ in_array('internet', old('features', [])) ? 'checked' : '' }}>
                            <label class="form-check-label">Internet Access</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="features[]" value="static_ip"
                                   {{ in_array('static_ip', old('features', [])) ? 'checked' : '' }}>
                            <label class="form-check-label">Static IP</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="features[]" value="priority_support"
                                   {{ in_array('priority_support', old('features', [])) ? 'checked' : '' }}>
                            <label class="form-check-label">Priority Support</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="features[]" value="unlimited_data"
                                   {{ in_array('unlimited_data', old('features', [])) ? 'checked' : '' }}>
                            <label class="form-check-label">Unlimited Data</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="features[]" value="free_installation"
                                   {{ in_array('free_installation', old('features', [])) ? 'checked' : '' }}>
                            <label class="form-check-label">Free Installation</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="features[]" value="wifi_router"
                                   {{ in_array('wifi_router', old('features', [])) ? 'checked' : '' }}>
                            <label class="form-check-label">WiFi Router Included</label>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Limits</label>
                    <div class="border rounded p-3">
                        <div class="mb-2">
                            <label class="form-label small">Data Limit (GB)</label>
                            <input type="number" class="form-control" name="limits[data_limit]" 
                                   value="{{ old('limits.data_limit') }}" placeholder="e.g., 1000">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Device Limit</label>
                            <input type="number" class="form-control" name="limits[device_limit]" 
                                   value="{{ old('limits.device_limit') }}" placeholder="e.g., 5">
                        </div>
                        <div>
                            <label class="form-label small">Speed Limit (Mbps)</label>
                            <input type="number" class="form-control" name="limits[speed_limit]" 
                                   value="{{ old('limits.speed_limit') }}" placeholder="e.g., 100">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" 
                               name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Create Plan
                </button>
                <a href="{{ route('service-plans.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function() {
    const slug = this.value
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    document.getElementById('slug').value = slug;
});
</script>
@endsection