<!-- resources/views/tickets/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="bi bi-ticket"></i> Ticket #{{ $ticket->ticket_number }}</h2>
        <p class="text-muted">{{ $ticket->title }}</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Ticket Details -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Priority:</strong> 
                            <span class="badge bg-{{ $ticket->priority === 'critical' ? 'danger' : 
                                                     ($ticket->priority === 'high' ? 'warning' : 
                                                     ($ticket->priority === 'medium' ? 'info' : 'secondary')) }}">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </p>
                        <p><strong>Category:</strong> {{ ucfirst($ticket->category) }}</p>
                        <p><strong>Status:</strong> 
                            <span class="badge bg-{{ $ticket->status === 'open' ? 'danger' : 
                                                     ($ticket->status === 'in_progress' ? 'warning' : 
                                                     ($ticket->status === 'resolved' ? 'success' : 'secondary')) }}">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Subscriber:</strong> {{ $ticket->subscriber->name ?? 'N/A' }}</p>
                        <p><strong>Device:</strong> {{ $ticket->device->name ?? 'N/A' }}</p>
                        <p><strong>Assigned To:</strong> {{ $ticket->assignedTo->name ?? 'Unassigned' }}</p>
                        <p><strong>Created By:</strong> {{ $ticket->createdBy->name ?? 'N/A' }}</p>
                    </div>
                </div>
                <hr>
                <p><strong>Description:</strong></p>
                <p class="text-muted">{{ $ticket->description }}</p>
                
                @if($ticket->resolved_at)
                    <hr>
                    <p><strong>Resolved:</strong> {{ $ticket->resolved_at->format('Y-m-d H:i:s') }}</p>
                    <p><strong>Resolution Time:</strong> {{ $ticket->resolution_time }} hours</p>
                @endif
            </div>
        </div>
        
        <!-- Comments -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-chat"></i> Comments</h6>
            </div>
            <div class="card-body">
                @forelse($ticket->comments as $comment)
                    <div class="mb-3 {{ $comment->is_internal ? 'bg-light p-3 rounded' : '' }}">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $comment->user->name ?? 'Unknown' }}</strong>
                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-0">{{ $comment->comment }}</p>
                        @if($comment->is_internal)
                            <small class="text-warning"><i class="bi bi-lock"></i> Internal Note</small>
                        @endif
                    </div>
                    @if(!$loop->last)
                        <hr>
                    @endif
                @empty
                    <p class="text-muted">No comments yet.</p>
                @endforelse
                
                <!-- Add Comment -->
                <hr>
                <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}">
                    @csrf
                    <div class="mb-2">
                        <textarea name="comment" class="form-control" rows="2" placeholder="Add a comment..." required></textarea>
                    </div>
                    <div class="mb-2 form-check">
                        <input type="checkbox" class="form-check-input" id="is_internal" name="is_internal">
                        <label class="form-check-label" for="is_internal">Internal Note (Only visible to staff)</label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-send"></i> Add Comment
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Actions Card -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Actions</h6>
            </div>
            <div class="card-body">
                <!-- Assign -->
                <form method="POST" action="{{ route('tickets.assign', $ticket) }}" class="mb-3">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Assign To:</label>
                        <select name="assigned_to" class="form-select" required>
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $ticket->assigned_to == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-person-check"></i> Assign
                    </button>
                </form>
                
                <!-- Update Status -->
                <form method="POST" action="{{ route('tickets.status', $ticket) }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Update Status:</label>
                        <select name="status" class="form-select" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ $ticket->status == $status ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">
                        <i class="bi bi-arrow-repeat"></i> Update Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection