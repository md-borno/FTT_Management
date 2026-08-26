<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class TopologyController extends Controller
{
    public function index()
    {
        // Eager loading all required relationships including 'nodes'
        $devices = Device::with(['deviceType', 'location', 'subscribers', 'nodes'])->get();
        
        $nodes = [];
        $links = [];
        
        // Create nodes from devices
        foreach ($devices as $device) {
            // Safe extraction from 'nodes' collection
            $node = $device->nodes->first();

            $x = $node?->x_position ?? rand(100, 900);
            $y = $node?->y_position ?? rand(100, 500);
            
            $nodes[] = [
                'id' => $device->id,
                'name' => $device->name,
                'type' => $device->deviceType->slug ?? 'unknown',
                'type_name' => $device->deviceType->name ?? 'Unknown',
                'status' => $device->status,
                'x' => $x,
                'y' => $y,
                'subscribers_count' => $device->subscribers->count(),
            ];
        }
        
        // Create links based on parent-child relationships
        foreach ($devices as $device) {
            if ($device->parent_device_id) {
                $links[] = [
                    'source' => $device->parent_device_id,
                    'target' => $device->id,
                ];
            }
        }
        
        // Fallback: If no links exist, create sample links for demonstration
        if (empty($links) && count($nodes) >= 2) {
            for ($i = 0; $i < min(5, count($nodes) - 1); $i++) {
                $links[] = [
                    'source' => $nodes[$i]['id'],
                    'target' => $nodes[$i + 1]['id'],
                ];
            }
        }
        
        return view('topology.index', compact('nodes', 'links'));
    }

    public function getData()
    {
        $devices = Device::with(['deviceType', 'location', 'nodes'])->get();
        
        $nodes = [];
        $links = [];
        
        foreach ($devices as $device) {
            $node = $device->nodes->first();

            $nodes[] = [
                'id' => $device->id,
                'label' => $device->name,
                'type' => $device->deviceType->slug ?? 'unknown',
                'status' => $device->status,
                'x' => $node?->x_position ?? rand(100, 900),
                'y' => $node?->y_position ?? rand(100, 500),
            ];
        }
        
        foreach ($devices as $device) {
            if ($device->parent_device_id) {
                $links[] = [
                    'source' => $device->parent_device_id,
                    'target' => $device->id,
                ];
            }
        }
        
        return response()->json([
            'nodes' => $nodes,
            'links' => $links,
        ]);
    }
}