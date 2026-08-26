<?php
// app/Http/Controllers/DeviceController.php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceType;
use App\Models\Location;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::with(['deviceType', 'location'])->paginate(20);
        return view('devices.index', compact('devices'));
    }

    public function create()
    {
        $deviceTypes = DeviceType::all();
        $locations = Location::all();
        return view('devices.create', compact('deviceTypes', 'locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'serial_number' => 'required|string|unique:devices',
            'ip_address' => 'nullable|ip',
            'mac_address' => 'nullable|string|unique:devices',
            'device_type_id' => 'required|exists:device_types,id',
            'location_id' => 'nullable|exists:locations,id',
            'status' => 'required|in:online,offline,maintenance,decommissioned',
        ]);
        
        Device::create($validated);
        return redirect()->route('devices.index')->with('success', 'Device created successfully.');
    }

    public function show(Device $device)
    {
        $device->load(['deviceType', 'location']);
        return view('devices.show', compact('device'));
    }

    public function edit(Device $device)
    {
        $deviceTypes = DeviceType::all();
        $locations = Location::all();
        return view('devices.edit', compact('device', 'deviceTypes', 'locations'));
    }

    public function update(Request $request, Device $device)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'nullable|ip',
            'device_type_id' => 'required|exists:device_types,id',
            'location_id' => 'nullable|exists:locations,id',
            'status' => 'required|in:online,offline,maintenance,decommissioned',
        ]);
        
        $device->update($validated);
        return redirect()->route('devices.index')->with('success', 'Device updated successfully.');
    }

    public function destroy(Device $device)
    {
        $device->delete();
        return redirect()->route('devices.index')->with('success', 'Device deleted successfully.');
    }
}