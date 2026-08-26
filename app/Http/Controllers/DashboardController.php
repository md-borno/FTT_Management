<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Subscriber;
use App\Models\Alarm;
use App\Models\Ticket;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalDevices = Device::count();
        $onlineDevices = Device::where('status', 'online')->count();
        $totalSubscribers = Subscriber::count();
        $activeSubscribers = Subscriber::where('status', 'active')->count();
        $activeAlarms = Alarm::whereNull('resolved_at')->count();
        $criticalAlarms = Alarm::where('severity', 'critical')->whereNull('resolved_at')->count();
        $openTickets = Ticket::whereIn('status', ['open', 'in_progress'])->count();

        return view('dashboard.index', compact(
            'totalDevices', 'onlineDevices', 'totalSubscribers', 
            'activeSubscribers', 'activeAlarms', 'criticalAlarms', 'openTickets'
        ));
    }
}