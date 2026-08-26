@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="bi bi-hdd-stack"></i> Devices</h2>
        <p class="text-muted">Manage network devices</p>
    </div>
    <a href="{{ route('devices.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Device
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Serial Number</th>
                        <th>IP Address</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($devices as $device)
                        <tr>
                            <td>{{ $device->name }}</td>
                            <td>{{ $device->serial_number }}</td>
                            <td>{{ $device->ip_address ?? 'N/A' }}</td>
                            <td>{{ $device->deviceType->name ?? 'N/A' }}</td>
                            <td>{{ $device->location->name ?? 'N/A' }}</td>
                            <td>
                                <span class="status-badge status-{{ $device->status }}">
                                    {{ ucfirst($device->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('devices.show', $device) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('devices.edit', $device) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('devices.destroy', $device) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Delete this device?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-4"></i>
                                <p class="mb-0">No devices found. Create your first device!</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $devices->links() }}
    </div>
</div>
@endsection