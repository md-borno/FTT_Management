@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="bi bi-people"></i> Subscribers</h2>
        <p class="text-muted">Manage your subscribers</p>
    </div>
    <a href="{{ route('subscribers.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Subscriber
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Customer ID</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscribers as $subscriber)
                        <tr>
                            <td>{{ $subscriber->name }}</td>
                            <td>{{ $subscriber->email }}</td>
                            <td>{{ $subscriber->phone ?? 'N/A' }}</td>
                            <td>{{ $subscriber->customer_id }}</td>
                            <td>{{ $subscriber->servicePlan->name ?? 'N/A' }}</td>
                            <td>
                                <span class="status-badge status-{{ $subscriber->status }}">
                                    {{ ucfirst($subscriber->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('subscribers.show', $subscriber) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('subscribers.edit', $subscriber) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('subscribers.destroy', $subscriber) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Delete this subscriber?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-4"></i>
                                <p class="mb-0">No subscribers found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $subscribers->links() }}
    </div>
</div>
@endsection