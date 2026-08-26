<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('serial_number')->unique();
            $table->string('ip_address')->nullable();
            $table->string('mac_address')->unique()->nullable();
            $table->string('firmware_version')->nullable();
            $table->string('model')->nullable();
            $table->string('manufacturer')->nullable();
            $table->foreignId('device_type_id')->constrained();
            $table->foreignId('location_id')->nullable()->constrained();
            $table->foreignId('parent_device_id')->nullable()->constrained('devices');
            $table->enum('status', ['online', 'offline', 'maintenance', 'decommissioned'])->default('offline');
            $table->json('configuration')->nullable();
            $table->json('capabilities')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('installed_at')->nullable();
            $table->timestamp('warranty_expiry')->nullable();
            $table->boolean('is_monitored')->default(true);
            $table->timestamps();
            
            $table->index(['status', 'last_seen_at']);
            $table->index('serial_number');
            $table->index('mac_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
