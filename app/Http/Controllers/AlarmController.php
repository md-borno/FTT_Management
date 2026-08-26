<?php
// app/Http/Controllers/AlarmController.php

namespace App\Http\Controllers;

use App\Models\Alarm;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlarmController extends Controller
{
    public function index(Request $request)
    {
        $query = Alarm::with(['device', 'acknowledgedBy', 'resolvedBy']);
        
        // Filter by severity
        if ($request->has('severity') && $request->severity != '') {
            $query->where('severity', $request->severity);
        }
        
        // Filter by status
        if ($request->has('status')) {
            if ($request->status == 'active') {
                $query->whereNull('resolved_at');
            } elseif ($request->status == 'resolved') {
                $query->whereNotNull('resolved_at');
            }
        }
        
        // Filter by device
        if ($request->has('device_id') && $request->device_id != '') {
            $query->where('device_id', $request->device_id);
        }
        
        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%");
            });
        }
        
        $alarms = $query->latest('occurred_at')->paginate(20);
        $devices = Device::all();
        $severities = ['critical', 'major', 'minor', 'warning'];
        
        // Statistics
        $stats = [
            'total' => Alarm::count(),
            'active' => Alarm::whereNull('resolved_at')->count(),
            'critical' => Alarm::where('severity', 'critical')->whereNull('resolved_at')->count(),
            'resolved_today' => Alarm::whereDate('resolved_at', today())->count(),
        ];
        
        return view('alarms.index', compact('alarms', 'devices', 'severities', 'stats'));
    }

    public function show(Alarm $alarm)
    {
        $alarm->load(['device', 'acknowledgedBy', 'resolvedBy']);
        return view('alarms.show', compact('alarm'));
    }

    public function acknowledge(Request $request, Alarm $alarm)
    {
        if ($alarm->resolved_at) {
            return back()->with('error', 'This alarm is already resolved.');
        }
        
        $alarm->update([
            'acknowledged_at' => now(),
            'acknowledged_by' => Auth::id(),
        ]);
        
        return back()->with('success', 'Alarm acknowledged successfully.');
    }

    public function resolve(Request $request, Alarm $alarm)
    {
        if ($alarm->resolved_at) {
            return back()->with('error', 'This alarm is already resolved.');
        }
        
        $request->validate([
            'resolution' => 'required|string|max:500',
        ]);
        
        $alarm->update([
            'resolved_at' => now(),
            'resolved_by' => Auth::id(),
            'resolution' => $request->resolution,
        ]);
        
        return back()->with('success', 'Alarm resolved successfully.');
    }

    public function bulkAction(Request $request)
    {
        $action = $request->action;
        $alarmIds = $request->alarm_ids ?? [];
        
        if (empty($alarmIds)) {
            return back()->with('error', 'No alarms selected.');
        }
        
        switch ($action) {
            case 'acknowledge':
                Alarm::whereIn('id', $alarmIds)
                    ->whereNull('resolved_at')
                    ->update([
                        'acknowledged_at' => now(),
                        'acknowledged_by' => Auth::id(),
                    ]);
                $message = 'Alarms acknowledged successfully.';
                break;
                
            case 'resolve':
                Alarm::whereIn('id', $alarmIds)
                    ->whereNull('resolved_at')
                    ->update([
                        'resolved_at' => now(),
                        'resolved_by' => Auth::id(),
                        'resolution' => 'Bulk resolved',
                    ]);
                $message = 'Alarms resolved successfully.';
                break;
                
            default:
                return back()->with('error', 'Invalid action.');
        }
        
        return back()->with('success', $message);
    }

    public function getStats()
    {
        $stats = [
            'total' => Alarm::count(),
            'active' => Alarm::whereNull('resolved_at')->count(),
            'critical' => Alarm::where('severity', 'critical')->whereNull('resolved_at')->count(),
            'acknowledged' => Alarm::whereNotNull('acknowledged_at')->whereNull('resolved_at')->count(),
        ];
        
        return response()->json($stats);
    }

  public function create()
{
    $devices = Device::all();
    $severities = ['critical', 'major', 'minor', 'warning'];
    return view('alarms.create', compact('devices', 'severities'));
}

public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'severity' => 'required|in:critical,major,minor,warning',
        'source' => 'required|string|max:255',
        'device_id' => 'required|exists:devices,id',
        'description' => 'required|string',
        'occurred_at' => 'required|date',
    ]);

    $alarm = Alarm::create([
        'name' => $validated['name'],
        'severity' => $validated['severity'],
        'source' => $validated['source'],
        'device_id' => $validated['device_id'],
        'description' => $validated['description'],
        'occurred_at' => $validated['occurred_at'],
    ]);

    return redirect()->route('alarms.index')
        ->with('success', 'Alarm created successfully!');
}
}