<!-- resources/views/tickets/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="bi bi-ticket"></i> Tickets</h2>
        <p class="text-muted">Manage support tickets</p>
    </div>
    <a href="{{ route('tickets.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create Ticket
    </a>
</div>

<!-- Statistics -->
<div class="row g-4 mb-4">
    <div class="col-md-2">
        <div class="stat-card text-center">
            <h5 class="text-muted">Total</h5>
            <h3>{{ $stats['total'] }}</h3>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card text-center">
            <h5 class="text-danger">Open</h5>
            <h3 class="text-danger">{{ $stats['open'] }}</h3>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card text-center">
            <h5 class="text-warning">In Progress</h5>
            <h3 class="text-warning">{{ $stats['in_progress'] }}</h3>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card text-center">
            <h5 class="text-info">Resolved</h5>
            <h3 class="text-info">{{ $stats['resolved'] }}</h3>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card text-center">
            <h5 class="text-secondary">Closed</h5>
            <h3>{{ $stats['closed'] }}</h3>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card text-center">
            <h5 class="text-danger">Critical</h5>
            <h3 class="text-danger">{{ $stats['critical'] }}</h3>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" 
                       placeholder="Search tickets..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="priority" class="form-select">
                    <option value="">All Priority</option>
                    @foreach($priorities as $priority)
                        <option value="{{ $priority }}" {{ request('priority') == $priority ? 'selected' : '' }}>
                            {{ ucfirst($priority) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                            {{ ucfirst($category) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Tickets Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Ticket #</th>
                        <th>Title</th>
                        <th>Priority</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr>
                            <td><strong>{{ $ticket->ticket_number }}</strong></td>
                            <td>{{ Str::limit($ticket->title, 30) }}</td>
                            <td>
                                <span class="badge bg-{{ $ticket->priority === 'critical' ? 'danger' : 
                                                         ($ticket->priority === 'high' ? 'warning' : 
                                                         ($ticket->priority === 'medium' ? 'info' : 'secondary')) }}">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td>{{ ucfirst($ticket->category) }}</td>
                            <td>
                                <span class="badge bg-{{ $ticket->status === 'open' ? 'danger' : 
                                                         ($ticket->status === 'in_progress' ? 'warning' : 
                                                         ($ticket->status === 'resolved' ? 'success' : 'secondary')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </td>
                            <td>{{ $ticket->assignedTo->name ?? 'Unassigned' }}</td>
                            <td>{{ $ticket->created_at->diffForHumans() }}</td>
                            <td>
                                <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-4"></i>
                                <p class="mb-0">No tickets found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $tickets->links() }}
    </div>
</div>
@endsection