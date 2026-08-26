<?php
// app/Http/Controllers/SubscriberController.php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Models\ServicePlan;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function index()
    {
        $subscribers = Subscriber::with('servicePlan')->paginate(20);
        return view('subscribers.index', compact('subscribers'));
    }

   public function create()
{
    $servicePlans = ServicePlan::where('is_active', true)->orderBy('sort_order')->get();
    return view('subscribers.create', compact('servicePlans'));
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:subscribers',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'customer_id' => 'required|string|unique:subscribers',
            'service_plan_id' => 'required|exists:service_plans,id',
            'status' => 'required|in:active,inactive,suspended,pending,cancelled',
        ]);

        Subscriber::create($validated);
        return redirect()->route('subscribers.index')->with('success', 'Subscriber created successfully.');
    }

    public function show(Subscriber $subscriber)
    {
        $subscriber->load('servicePlan');
        return view('subscribers.show', compact('subscriber'));
    }

   public function edit(Subscriber $subscriber)
{
    $servicePlans = ServicePlan::where('is_active', true)->orderBy('sort_order')->get();
    return view('subscribers.edit', compact('subscriber', 'servicePlans'));
}

    public function update(Request $request, Subscriber $subscriber)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:subscribers,email,' . $subscriber->id,
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'service_plan_id' => 'required|exists:service_plans,id',
            'status' => 'required|in:active,inactive,suspended,pending,cancelled',
        ]);

        $subscriber->update($validated);
        return redirect()->route('subscribers.index')->with('success', 'Subscriber updated successfully.');
    }

    public function destroy(Subscriber $subscriber)
    {
        $subscriber->delete();
        return redirect()->route('subscribers.index')->with('success', 'Subscriber deleted successfully.');
    }
}