<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\Subscriber;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['subscriber', 'device', 'assignedTo', 'createdBy']);
        
        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter by priority
        if ($request->has('priority') && $request->priority != '') {
            $query->where('priority', $request->priority);
        }
        
        // Filter by category
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }
        
        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('ticket_number', 'like', "%{$search}%");
            });
        }
        
        $tickets = $query->latest()->paginate(20);
        $statuses = ['open', 'in_progress', 'resolved', 'closed'];
        $priorities = ['low', 'medium', 'high', 'critical'];
        $categories = ['technical', 'billing', 'maintenance', 'installation', 'other'];
        
        // Statistics
        $stats = [
            'total' => Ticket::count(),
            'open' => Ticket::where('status', 'open')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'resolved' => Ticket::where('status', 'resolved')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
            'critical' => Ticket::where('priority', 'critical')->whereIn('status', ['open', 'in_progress'])->count(),
        ];
        
        return view('tickets.index', compact('tickets', 'statuses', 'priorities', 'categories', 'stats'));
    }

    public function create()
    {
        $subscribers = Subscriber::all();
        $devices = Device::all();
        $users = User::all();
        $priorities = ['low', 'medium', 'high', 'critical'];
        $categories = ['technical', 'billing', 'maintenance', 'installation', 'other'];
        
        return view('tickets.create', compact('subscribers', 'devices', 'users', 'priorities', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'category' => 'required|in:technical,billing,maintenance,installation,other',
            'subscriber_id' => 'nullable|exists:subscribers,id',
            'device_id' => 'nullable|exists:devices,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);
        
        $validated['ticket_number'] = 'TKT-' . strtoupper(Str::random(8));
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'open';
        
        $ticket = Ticket::create($validated);
        
        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket created successfully!');
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['subscriber', 'device', 'assignedTo', 'createdBy', 'comments.user']);
        $users = User::all();
        $statuses = ['open', 'in_progress', 'resolved', 'closed'];
        
        return view('tickets.show', compact('ticket', 'users', 'statuses'));
    }

    public function edit(Ticket $ticket)
    {
        $subscribers = Subscriber::all();
        $devices = Device::all();
        $users = User::all();
        $priorities = ['low', 'medium', 'high', 'critical'];
        $categories = ['technical', 'billing', 'maintenance', 'installation', 'other'];
        $statuses = ['open', 'in_progress', 'resolved', 'closed'];
        
        return view('tickets.edit', compact('ticket', 'subscribers', 'devices', 'users', 'priorities', 'categories', 'statuses'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'category' => 'required|in:technical,billing,maintenance,installation,other',
            'subscriber_id' => 'nullable|exists:subscribers,id',
            'device_id' => 'nullable|exists:devices,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);
        
        if ($validated['status'] === 'resolved' && $ticket->status !== 'resolved') {
            $validated['resolved_at'] = now();
            $validated['resolution_time'] = now()->diffInHours($ticket->created_at);
        }
        
        if ($validated['status'] === 'closed' && $ticket->status !== 'closed') {
            $validated['closed_at'] = now();
        }
        
        $ticket->update($validated);
        
        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket updated successfully!');
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('tickets.index')
            ->with('success', 'Ticket deleted successfully!');
    }

    public function addComment(Request $request, Ticket $ticket)
    {
        $request->validate([
            'comment' => 'required|string',
            'is_internal' => 'boolean',
        ]);
        
        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
            'is_internal' => $request->has('is_internal'),
        ]);
        
        // If status is open and someone comments, move to in_progress
        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }
        
        return back()->with('success', 'Comment added successfully!');
    }

    public function assign(Request $request, Ticket $ticket)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);
        
        $ticket->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'in_progress',
        ]);
        
        return back()->with('success', 'Ticket assigned successfully!');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);
        
        $data = ['status' => $request->status];
        
        if ($request->status === 'resolved') {
            $data['resolved_at'] = now();
            $data['resolution_time'] = now()->diffInHours($ticket->created_at);
        }
        
        if ($request->status === 'closed') {
            $data['closed_at'] = now();
        }
        
        $ticket->update($data);
        
        return back()->with('success', 'Ticket status updated successfully!');
    }
}