<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\TopologyController;
use App\Http\Controllers\AlarmController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServicePlanController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

Route::get('/', function () {
    return redirect('/dashboard');
});

// Login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
})->name('login');

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', function () {
        return view('profile.edit');
    })->name('profile.edit'); 
    
    Route::put('/profile', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);
        
        $user->update($request->only('name', 'email'));
        return redirect('/profile')->with('success', 'Profile updated successfully!');
    })->name('profile.update'); 
    
    // Devices
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    Route::get('/devices/create', [DeviceController::class, 'create'])->name('devices.create');
    Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
    Route::get('/devices/{device}', [DeviceController::class, 'show'])->name('devices.show');
    Route::get('/devices/{device}/edit', [DeviceController::class, 'edit'])->name('devices.edit');
    Route::put('/devices/{device}', [DeviceController::class, 'update'])->name('devices.update');
    Route::delete('/devices/{device}', [DeviceController::class, 'destroy'])->name('devices.destroy');
    
    // Subscribers
    Route::get('/subscribers', [SubscriberController::class, 'index'])->name('subscribers.index');
    Route::get('/subscribers/create', [SubscriberController::class, 'create'])->name('subscribers.create');
    Route::post('/subscribers', [SubscriberController::class, 'store'])->name('subscribers.store');
    Route::get('/subscribers/{subscriber}', [SubscriberController::class, 'show'])->name('subscribers.show');
    Route::get('/subscribers/{subscriber}/edit', [SubscriberController::class, 'edit'])->name('subscribers.edit');
    Route::put('/subscribers/{subscriber}', [SubscriberController::class, 'update'])->name('subscribers.update');
    Route::delete('/subscribers/{subscriber}', [SubscriberController::class, 'destroy'])->name('subscribers.destroy');

    // Alarm Routes
    Route::get('/alarms', [AlarmController::class, 'index'])->name('alarms.index');
Route::get('/alarms/create', [AlarmController::class, 'create'])->name('alarms.create');  // ADD THIS
Route::post('/alarms', [AlarmController::class, 'store'])->name('alarms.store');     
    Route::get('/alarms/{alarm}', [AlarmController::class, 'show'])->name('alarms.show');
    Route::post('/alarms/{alarm}/acknowledge', [AlarmController::class, 'acknowledge'])->name('alarms.acknowledge');
    Route::post('/alarms/{alarm}/resolve', [AlarmController::class, 'resolve'])->name('alarms.resolve');
    Route::post('/alarms/bulk-action', [AlarmController::class, 'bulkAction'])->name('alarms.bulk-action');
    Route::get('/alarms/stats', [AlarmController::class, 'getStats'])->name('alarms.stats');
    
    // Ticket Routes
    Route::resource('tickets', TicketController::class);
    Route::post('/tickets/{ticket}/comments', [TicketController::class, 'addComment'])->name('tickets.comments.store');
    Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign'])->name('tickets.assign');
    Route::post('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.status');
    
    // Topology Routes
    Route::get('/topology', [TopologyController::class, 'index'])->name('topology.index');
    Route::get('/topology/data', [TopologyController::class, 'getData'])->name('topology.data');

    // Service Plans - Add this inside the auth middleware group
Route::resource('service-plans', ServicePlanController::class);
Route::post('/service-plans/{servicePlan}/toggle-status', [ServicePlanController::class, 'toggleStatus'])->name('service-plans.toggle-status');

// Ticket Routes
Route::resource('tickets', TicketController::class);
Route::post('/tickets/{ticket}/comments', [TicketController::class, 'addComment'])->name('tickets.comments.store');
Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign'])->name('tickets.assign');
Route::post('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.status');
});