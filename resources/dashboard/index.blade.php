{{-- resources/views/dashboard/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard - FTTX Manager')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">Dashboard</h1>
            <p class="text-muted">Network overview and statistics</p>
        </div>
        <div>
            <button class="btn btn-primary btn-sm" onclick="refreshData()">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small mb-1">Total Devices</p>
                    <h2 class="mb-0">{{ $stats['total_devices'] }}</h2>
                    <small class="text-success">
                        <i class="bi bi-arrow-up"></i> {{ $stats['online_devices'] }} online
                    </small>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-hdd-stack"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small mb-1">Subscribers</p>
                    <h2 class="mb-0">{{ $stats['total_subscribers'] }}</h2>
                    <small class="text-success">
                        <i class="bi bi-arrow-up"></i> {{ $stats['active_subscribers'] }} active
                    </small>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small mb-1">Active Alarms</p>
                    <h2 class="mb-0">{{ $stats['active_alarms'] }}</h2>
                    <small class="text-danger">
                        <i class="bi bi-exclamation-triangle"></i> {{ $stats['critical_alarms'] }} critical
                    </small>
                </div>
                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-bell"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small mb-1">Open Tickets</p>
                    <h2 class="mb-0">{{ $stats['open_tickets'] }}</h2>
                    <small class="text-warning">
                        <i class="bi bi-clock"></i> Awaiting resolution
                    </small>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-ticket"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-graph-up"></i> Network Traffic (Last 24 Hours)</h6>
            </div>
            <div class="card-body">
                <canvas id="trafficChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-pie-chart"></i> Device Status</h6>
            </div>
            <div class="card-body">
                <canvas id="deviceStatusChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Row -->
<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-bell"></i> Recent Alarms</h6>
                <a href="{{ route('alarms.index') }}" class="btn btn-sm btn-link">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($stats['recent_alarms'] as $alarm)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-{{ $alarm->severity_color }} me-2">
                                    {{ strtoupper($alarm->severity) }}
                                </span>
                                {{ $alarm->name }}
                                <small class="d-block text-muted">{{ $alarm->device->name ?? 'Unknown Device' }}</small>
                            </div>
                            <small class="text-muted">{{ $alarm->age }}</small>
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted py-4">
                            <i class="bi bi-check-circle fs-4"></i>
                            <p class="mb-0">No recent alarms</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-ticket"></i> Recent Tickets</h6>
                <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-link">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($stats['recent_tickets'] as $ticket)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-{{ $ticket->priority === 'critical' ? 'danger' : ($ticket->priority === 'high' ? 'warning' : 'secondary') }} me-2">
                                    {{ strtoupper($ticket->priority) }}
                                </span>
                                {{ $ticket->title }}
                                <small class="d-block text-muted">
                                    {{ $ticket->subscriber->name ?? 'Unknown' }} 
                                    @if($ticket->assignedTo)
                                        • Assigned to {{ $ticket->assignedTo->name }}
                                    @endif
                                </small>
                            </div>
                            <span class="badge bg-{{ $ticket->status === 'open' ? 'info' : ($ticket->status === 'in_progress' ? 'warning' : 'success') }}">
                                {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
                            </span>
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted py-4">
                            <i class="bi bi-check-circle fs-4"></i>
                            <p class="mb-0">No recent tickets</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Traffic Chart
    const trafficCtx = document.getElementById('trafficChart').getContext('2d');
    new Chart(trafficCtx, {
        type: 'line',
        data: {
            labels: ['00:00', '02:00', '04:00', '06:00', '08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00', '22:00'],
            datasets: [{
                label: 'Bandwidth (Mbps)',
                data: [45, 32, 28, 35, 89, 145, 178, 234, 198, 167, 98, 56],
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Active Users',
                data: [120, 98, 76, 145, 345, 567, 678, 789, 654, 543, 321, 156],
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: true,
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                y1: {
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Device Status Chart
    const deviceStatusData = @json($stats['device_status_distribution']);
    const statusCtx = document.getElementById('deviceStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: deviceStatusData.map(d => d.status.charAt(0).toUpperCase() + d.status.slice(1)),
            datasets: [{
                data: deviceStatusData.map(d => d.count),
                backgroundColor: ['#28a745', '#dc3545', '#ffc107', '#6c757d'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 10,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    function refreshData() {
        // Show loading state
        const btn = document.querySelector('.btn-primary');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Refreshing...';
        btn.disabled = true;
        
        // Simulate refresh
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }
</script>
@endpush