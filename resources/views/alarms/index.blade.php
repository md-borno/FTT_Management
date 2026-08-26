<!-- resources/views/alarms/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="bi bi-bell"></i> Alarms</h2>
        <p class="text-muted">Monitor and manage network alarms</p>
    </div>
    <div>
        <!-- Add Create Alarm Button -->
        <a href="{{ route('alarms.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create Alarm
        </a>
        <button class="btn btn-success" onclick="window.location.reload()">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted small mb-1">Total Alarms</p>
                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                </div>
                <div class="stat-icon bg-secondary bg-opacity-10 text-secondary">
                    <i class="bi bi-bell"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted small mb-1">Active Alarms</p>
                    <h3 class="mb-0 text-danger">{{ $stats['active'] }}</h3>
                </div>
                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted small mb-1">Critical</p>
                    <h3 class="mb-0 text-danger">{{ $stats['critical'] }}</h3>
                </div>
                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-exclamation-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted small mb-1">Resolved Today</p>
                    <h3 class="mb-0 text-success">{{ $stats['resolved_today'] }}</h3>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alarms Table -->
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('alarms.bulk-action') }}" id="bulkForm">
            @csrf
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll" onclick="toggleSelectAll()">
                            </th>
                            <th>Severity</th>
                            <th>Name</th>
                            <th>Device</th>
                            <th>Source</th>
                            <th>Occurred</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alarms as $alarm)
                            <tr class="alarm-{{ $alarm->severity }}">
                                <td>
                                    @if(!$alarm->resolved_at)
                                        <input type="checkbox" name="alarm_ids[]" value="{{ $alarm->id }}" class="alarm-checkbox">
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $alarm->severity_color }}">
                                        {{ strtoupper($alarm->severity) }}
                                    </span>
                                </td>
                                <td>{{ $alarm->name }}</td>
                                <td>{{ $alarm->device->name ?? 'N/A' }}</td>
                                <td>{{ $alarm->source }}</td>
                                <td>{{ $alarm->occurred_at->diffForHumans() }}</td>
                                <td>
                                    @if($alarm->resolved_at)
                                        <span class="badge bg-success">Resolved</span>
                                    @elseif($alarm->acknowledged_at)
                                        <span class="badge bg-warning">Acknowledged</span>
                                    @else
                                        <span class="badge bg-danger">Active</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('alarms.show', $alarm) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(!$alarm->resolved_at)
                                        @if(!$alarm->acknowledged_at)
                                            <form action="{{ route('alarms.acknowledge', $alarm) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('alarms.resolve', $alarm) }}" method="POST" class="d-inline" 
                                              onsubmit="return confirm('Resolve this alarm?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="bi bi-check2-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-check-circle fs-4 text-success"></i>
                                    <p class="mb-0">No alarms found. Everything is running smoothly!</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($alarms->whereNull('resolved_at')->count() > 0)
            <div class="mt-3">
                <div class="btn-group">
                    <button type="button" class="btn btn-warning" onclick="bulkAction('acknowledge')">
                        <i class="bi bi-check-circle"></i> Acknowledge Selected
                    </button>
                    <button type="button" class="btn btn-success" onclick="bulkAction('resolve')">
                        <i class="bi bi-check2-circle"></i> Resolve Selected
                    </button>
                </div>
            </div>
            @endif
        </form>
        
        {{ $alarms->links() }}
    </div>
</div>

<script>
function toggleSelectAll() {
    const checked = document.getElementById('selectAll').checked;
    document.querySelectorAll('.alarm-checkbox').forEach(cb => cb.checked = checked);
}

function bulkAction(action) {
    const selected = document.querySelectorAll('.alarm-checkbox:checked');
    if (selected.length === 0) {
        alert('Please select at least one alarm.');
        return;
    }
    
    if (confirm(`Are you sure you want to ${action} ${selected.length} alarm(s)?`)) {
        const form = document.getElementById('bulkForm');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'action';
        input.value = action;
        form.appendChild(input);
        form.submit();
    }
}
</script>

<style>
.alarm-critical { background-color: #fff5f5; }
.alarm-major { background-color: #fff8f0; }
.alarm-minor { background-color: #f0f8ff; }
.alarm-warning { background-color: #f5f5f5; }
</style>
@endsection