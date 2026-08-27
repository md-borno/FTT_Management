@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="bi bi-tags"></i> Service Plans</h2>
        <p class="text-muted">Manage subscriber service plans</p>
    </div>
    <a href="{{ route('service-plans.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create Plan
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Bandwidth</th>
                        <th>Price</th>
                        <th>Billing</th>
                        <th>Subscribers</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                        <tr>
                            <td>
                                <strong>{{ $plan->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $plan->slug }}</small>
                            </td>
                            <td>{{ $plan->bandwidth ?? 'N/A' }}</td>
                            <td>
                                <strong>${{ number_format($plan->price, 2) }}</strong>
                                <br>
                                <small class="text-muted">/ {{ $plan->billing_cycle }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ ucfirst($plan->billing_cycle) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-primary">
                                    {{ $plan->subscribers_count ?? 0 }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $plan->is_active ? 'success' : 'danger' }}">
                                    {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('service-plans.show', $plan) }}" 
                                   class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('service-plans.edit', $plan) }}" 
                                   class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('service-plans.toggle-status', $plan) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-{{ $plan->is_active ? 'secondary' : 'success' }}">
                                        <i class="bi bi-{{ $plan->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('service-plans.destroy', $plan) }}" 
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this plan?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-4"></i>
                                <p class="mb-0">No service plans found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $plans->links() }}
    </div>
</div>
@endsection