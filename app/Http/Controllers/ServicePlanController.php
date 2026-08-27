<?php
namespace App\Http\Controllers;

use App\Models\ServicePlan;
use Illuminate\Http\Request;

class ServicePlanController extends Controller
{
    public function index()
    {
        $plans = ServicePlan::orderBy('sort_order')->paginate(20);
        return view('service-plans.index', compact('plans'));
    }

    public function create()
    {
        return view('service-plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:service_plans',
            'description' => 'nullable|string',
            'bandwidth' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,quarterly,yearly',
            'features' => 'nullable|array',
            'limits' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['features'] = json_encode($request->features ?? []);
        $validated['limits'] = json_encode($request->limits ?? []);

        ServicePlan::create($validated);

        return redirect()->route('service-plans.index')
            ->with('success', 'Service Plan created successfully!');
    }

    public function show(ServicePlan $servicePlan)
    {
        return view('service-plans.show', compact('servicePlan'));
    }

    public function edit(ServicePlan $servicePlan)
    {
        return view('service-plans.edit', compact('servicePlan'));
    }

    public function update(Request $request, ServicePlan $servicePlan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:service_plans,slug,' . $servicePlan->id,
            'description' => 'nullable|string',
            'bandwidth' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,quarterly,yearly',
            'features' => 'nullable|array',
            'limits' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['features'] = json_encode($request->features ?? []);
        $validated['limits'] = json_encode($request->limits ?? []);

        $servicePlan->update($validated);

        return redirect()->route('service-plans.index')
            ->with('success', 'Service Plan updated successfully!');
    }

    public function destroy(ServicePlan $servicePlan)
    {
        // Check if plan has subscribers
        if ($servicePlan->subscribers()->count() > 0) {
            return back()->with('error', 'Cannot delete plan with active subscribers!');
        }

        $servicePlan->delete();

        return redirect()->route('service-plans.index')
            ->with('success', 'Service Plan deleted successfully!');
    }

    public function toggleStatus(ServicePlan $servicePlan)
    {
        $servicePlan->update(['is_active' => !$servicePlan->is_active]);
        
        return back()->with('success', 'Service Plan status updated!');
    }
}