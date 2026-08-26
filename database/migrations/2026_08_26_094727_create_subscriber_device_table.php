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
        Schema::create('subscriber_device', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscriber_id')->constrained()->onDelete('cascade');
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('deactivated_at')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            
            $table->unique(['subscriber_id', 'device_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriber_device');
    }
};
