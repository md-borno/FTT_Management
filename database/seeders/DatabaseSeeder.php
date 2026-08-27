<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DeviceType;
use App\Models\Location;
use App\Models\ServicePlan;
use App\Models\Device;
use App\Models\Subscriber;
use App\Models\Node;  
use App\Models\Link;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->command->info('=========================================');
        $this->command->info('Starting Database Seeding');
        $this->command->info('=========================================');

        // 1. Admin User
        $this->command->info('Creating admin user...');
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@fttx.com',
            'password' => Hash::make('password123'),
        ]);

        // 2. Device Types
        $this->command->info('Creating device types...');
        $oltType = DeviceType::create([
            'name' => 'OLT',
            'slug' => 'olt',
            'description' => 'Optical Line Terminal',
            'is_active' => true,
        ]);
        
        $ontType = DeviceType::create([
            'name' => 'ONT',
            'slug' => 'ont',
            'description' => 'Optical Network Terminal',
            'is_active' => true,
        ]);

        // 3. Location
        $this->command->info('Creating location...');
        $location = Location::create([
            'name' => 'Data Center',
            'code' => 'DC-01',
            'city' => 'New York',
            'country' => 'US',
            'is_active' => true,
        ]);

        // 4. Service Plan
        $this->command->info('Creating service plan...');
        $plan = ServicePlan::create([
            'name' => 'Standard',
            'slug' => 'standard',
            'bandwidth' => '100 Mbps',
            'price' => 49.99,
            'is_active' => true,
        ]);

        // 5. Create OLT
        $this->command->info('Creating OLT device...');
        $olt = Device::create([
            'name' => 'OLT-01',
            'serial_number' => 'OLT' . rand(100000, 999999),
            'ip_address' => '192.168.1.1',
            'mac_address' => '00:11:22:33:44:55',
            'firmware_version' => 'v3.2.1',
            'model' => 'Huawei MA5800',
            'manufacturer' => 'Huawei',
            'device_type_id' => $oltType->id,
            'location_id' => $location->id,
            'status' => 'online',
            'last_seen_at' => now(),
            'installed_at' => now()->subMonths(6),
            'is_monitored' => true,
        ]);

        // 6. Create ONTs
        $this->command->info('Creating ONT devices...');
        $onts = [];
        for ($i = 1; $i <= 5; $i++) {
            $ont = Device::create([
                'name' => "ONT-0{$i}",
                'serial_number' => 'ONT' . rand(100000, 999999),
                'ip_address' => "192.168.1.10{$i}",
                'mac_address' => '00:11:22:33:44:' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'firmware_version' => 'v2.1.3',
                'model' => 'Huawei HG8245',
                'manufacturer' => 'Huawei',
                'device_type_id' => $ontType->id,
                'location_id' => $location->id,
                'parent_device_id' => $olt->id,
                'status' => 'online',
                'last_seen_at' => now(),
                'installed_at' => now()->subMonths(rand(1, 3)),
                'is_monitored' => true,
            ]);
            $onts[] = $ont;
        }

        // 7. Create Subscriber
        $this->command->info('Creating subscriber...');
        $subscriber = Subscriber::create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '+1-555-000-1111',
            'address' => '123 Main Street, New York, NY 10001',
            'customer_id' => 'CUST' . rand(10000, 99999),
            'service_plan_id' => $plan->id,
            'status' => 'active',
            'activated_at' => now(),
            'data_usage' => rand(100, 5000),
            'is_priority' => false,
        ]);

        // Assign device to subscriber
        if (!empty($onts)) {
            $subscriber->devices()->attach($onts[0]->id, [
                'status' => 'active',
                'assigned_at' => now(),
            ]);
        }

        // 8. Run Topology Seeder
        $this->command->info('Creating network topology...');
        $this->call(TopologySeeder::class);

        $this->command->info('=========================================');
        $this->command->info(' Database seeding completed successfully!');
        $this->command->info('=========================================');
        $this->command->info('');
        $this->command->info(' Summary:');
        $this->command->info('  - 1 Admin User');
        $this->command->info('  - 2 Device Types (OLT, ONT)');
        $this->command->info('  - 1 Location');
        $this->command->info('  - 1 Service Plan');
        $this->command->info('  - 6 Devices (1 OLT, 5 ONTs)');
        $this->command->info('  - 1 Subscriber');
        $this->command->info('  - ' . Node::count() . ' Topology Nodes');
        $this->command->info('  - ' . Link::count() . ' Topology Links');
        $this->command->info('');
        $this->command->info(' Login Credentials:');
        $this->command->info('  Email: admin@fttx.com');
        $this->command->info('  Password: password123');
        $this->command->info('=========================================');
    }
}