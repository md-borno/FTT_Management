<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Node;
use App\Models\Link;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TopologySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->command->info('=========================================');
        $this->command->info('Seeding Network Topology');
        $this->command->info('=========================================');

        // Get all devices
        $devices = Device::with('deviceType')->get();

        if ($devices->isEmpty()) {
            $this->command->error('No devices found! Please run DatabaseSeeder first.');
            return;
        }

        $this->command->info('Creating nodes from devices...');

        // Clear existing topology data safely by disabling FK checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Link::truncate();
        Node::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create nodes from devices
        foreach ($devices as $index => $device) {
            // Get position based on device type
            $position = $this->getNodePosition($device, $index, $devices->count());
            
            $node = Node::create([
                'name' => $device->name . '-Node',
                'type' => $device->deviceType->slug ?? 'unknown',
                'device_id' => $device->id,
                'location_id' => $device->location_id ?? 1,
                'x_position' => $position['x'],
                'y_position' => $position['y'],
                'properties' => [
                    'device_model' => $device->model ?? 'N/A',
                    'firmware' => $device->firmware_version ?? 'N/A',
                ],
                'is_active' => true,
            ]);
            
            $this->command->info("Node created: {$device->name} at ({$position['x']}, {$position['y']})");
        }

        $this->command->info(Node::count() . ' nodes created');

        // Create links based on parent-child relationships
        $this->command->info('Creating links between nodes...');

        foreach ($devices as $device) {
            if ($device->parent_device_id) {
                $sourceNode = Node::where('device_id', $device->parent_device_id)->first();
                $targetNode = Node::where('device_id', $device->id)->first();

                if ($sourceNode && $targetNode) {
                    Link::create([
                        'source_node_id' => $sourceNode->id,
                        'target_node_id' => $targetNode->id,
                        'type' => 'fiber',
                        'status' => 'active',
                        'distance' => rand(100, 1000),
                        'capacity' => rand(100, 1000),
                        'properties' => [
                            'fiber_type' => 'SMF',
                            'connectors' => 'SC/APC',
                        ],
                    ]);
                    
                    $this->command->info(" Link created: {$sourceNode->name} → {$targetNode->name}");
                }
            }
        }

        // If no links created, create a star topology
        if (Link::count() == 0) {
            $this->command->info('Creating star topology...');
            $nodes = Node::all();
            
            if ($nodes->count() >= 2) {
                // Find OLT node (center)
                $centerNode = $nodes->where('type', 'olt')->first() ?? $nodes->first();
                
                foreach ($nodes as $node) {
                    if ($node->id !== $centerNode->id) {
                        Link::create([
                            'source_node_id' => $centerNode->id,
                            'target_node_id' => $node->id,
                            'type' => 'fiber',
                            'status' => 'active',
                            'distance' => rand(100, 500),
                            'capacity' => rand(100, 1000),
                        ]);
                    }
                }
                $this->command->info('Star topology created with ' . Link::count() . ' links');
            }
        }

        $this->command->info('=========================================');
        $this->command->info('Topology seeding completed!');
        $this->command->info('   - ' . Node::count() . ' nodes created');
        $this->command->info('   - ' . Link::count() . ' links created');
        $this->command->info('=========================================');
    }

    /**
     * Get node position based on device type
     */
    private function getNodePosition($device, $index, $total): array
    {
        $type = $device->deviceType->slug ?? 'unknown';
        
        // Positions for different device types
        $positions = [
            'olt' => ['x' => 400, 'y' => 300],
            'switch' => ['x' => 300, 'y' => 200],
            'router' => ['x' => 500, 'y' => 200],
            'splitter' => ['x' => 300, 'y' => 400],
            'ont' => [],
            'default' => [],
        ];

        // If it's an OLT, place it in the center
        if ($type === 'olt') {
            return $positions['olt'];
        }

        // For ONT, distribute in a circle around OLT
        if ($type === 'ont') {
            $ontCount = Device::whereHas('deviceType', function($q) {
                $q->where('slug', 'ont');
            })->count();
            
            $ontIndex = Device::whereHas('deviceType', function($q) {
                $q->where('slug', 'ont');
            })->pluck('id')->search($device->id);
            
            $angle = ($ontIndex / max(1, $ontCount)) * 2 * M_PI;
            $radius = 200;
            
            return [
                'x' => 400 + $radius * cos($angle),
                'y' => 300 + $radius * sin($angle),
            ];
        }

        // For other devices, check if they have a parent
        if ($device->parent_device_id) {
            $parentNode = Node::where('device_id', $device->parent_device_id)->first();
            if ($parentNode) {
                return [
                    'x' => $parentNode->x_position + rand(-80, 80),
                    'y' => $parentNode->y_position + rand(-80, 80),
                ];
            }
        }

        // Default random position
        return [
            'x' => rand(100, 700),
            'y' => rand(100, 500),
        ];
    }
}