@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="bi bi-grid"></i> Dashboard</h2>
        <p class="text-muted">Network overview and statistics</p>
    </div>
</div>

<!-- Quick Navigation Cards -->
<div class="row g-3 mb-4">
    <div class="col-md">
        <a href="{{ route('devices.index') }}" class="text-decoration-none">
            <div class="stat-card text-center">
                <i class="bi bi-hdd-stack fs-1 text-primary"></i>
                <h6 class="mt-2 mb-0">Devices</h6>
                <small class="text-muted">Manage network devices</small>
            </div>
        </a>
    </div>
    <div class="col-md">
        <a href="{{ route('subscribers.index') }}" class="text-decoration-none">
            <div class="stat-card text-center">
                <i class="bi bi-people fs-1 text-success"></i>
                <h6 class="mt-2 mb-0">Subscribers</h6>
                <small class="text-muted">Manage customers</small>
            </div>
        </a>
    </div>
    <div class="col-md">
        <a href="{{ route('alarms.index') }}" class="text-decoration-none">
            <div class="stat-card text-center">
                <i class="bi bi-bell fs-1 text-danger"></i>
                <h6 class="mt-2 mb-0">Alarms</h6>
                <small class="text-muted">View network alarms</small>
            </div>
        </a>
    </div>
    <div class="col-md">
        <a href="{{ route('tickets.index') }}" class="text-decoration-none">
            <div class="stat-card text-center">
                <i class="bi bi-ticket fs-1 text-warning"></i>
                <h6 class="mt-2 mb-0">Tickets</h6>
                <small class="text-muted">Support tickets</small>
            </div>
        </a>
    </div>
    <div class="col-md">
        <a href="{{ route('topology.index') }}" class="text-decoration-none">
            <div class="stat-card text-center">
                <i class="bi bi-diagram-3 fs-1 text-info"></i>
                <h6 class="mt-2 mb-0">Topology</h6>
                <small class="text-muted">Network diagram</small>
            </div>
        </a>
    </div>
    <div class="col-md">
        <a href="{{ route('profile.edit') }}" class="text-decoration-none">
            <div class="stat-card text-center">
                <i class="bi bi-person fs-1 text-secondary"></i>
                <h6 class="mt-2 mb-0">Profile</h6>
                <small class="text-muted">Your account</small>
            </div>
        </a>
    </div>
    <div class="col-md">
        <a href="{{ route('service-plans.index') }}" class="text-decoration-none">
            <div class="stat-card text-center">
                <i class="bi bi-person fs-1 text-secondary"></i>
                <h6 class="mt-2 mb-0">Service Plans</h6>
                <small class="text-muted">Manage service offerings</small>
            </div>
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted small mb-1">Total Devices</p>
                    <h3 class="mb-0">{{ $totalDevices ?? 0 }}</h3>
                    <small class="text-success">{{ $onlineDevices ?? 0 }} online</small>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-hdd-stack"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted small mb-1">Subscribers</p>
                    <h3 class="mb-0">{{ $totalSubscribers ?? 0 }}</h3>
                    <small class="text-success">{{ $activeSubscribers ?? 0 }} active</small>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted small mb-1">Active Alarms</p>
                    <h3 class="mb-0">{{ $activeAlarms ?? 0 }}</h3>
                    <small class="text-danger">{{ $criticalAlarms ?? 0 }} critical</small>
                </div>
                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-bell"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted small mb-1">Open Tickets</p>
                    <h3 class="mb-0">{{ $openTickets ?? 0 }}</h3>
                    <small class="text-warning">Needs attention</small>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-ticket"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links Section -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-link-45deg"></i> Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="{{ route('devices.create') }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-plus-circle"></i> Add Device
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('subscribers.create') }}" class="btn btn-outline-success w-100">
                            <i class="bi bi-plus-circle"></i> Add Subscriber
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('tickets.create') }}" class="btn btn-outline-warning w-100">
                            <i class="bi bi-plus-circle"></i> Create Ticket
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('topology.index') }}" class="btn btn-outline-info w-100">
                            <i class="bi bi-diagram-3"></i> View Topology
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Information -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> System Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <p><strong>Laravel Version:</strong> {{ app()->version() }}</p>
                        <p><strong>PHP Version:</strong> {{ phpversion() }}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Environment:</strong> {{ app()->environment() }}</p>
                        <p><strong>Debug Mode:</strong> {{ config('app.debug') ? 'Enabled' : 'Disabled' }}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Server Time:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
                        <p><strong>Timezone:</strong> {{ config('app.timezone') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection