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
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_node_id')->constrained('nodes')->onDelete('cascade');
            $table->foreignId('target_node_id')->constrained('nodes')->onDelete('cascade');
            $table->string('type')->default('fiber'); // fiber, copper, wireless
            $table->string('status')->default('active'); // active, inactive, maintenance
            $table->float('distance')->nullable(); // in meters
            $table->float('capacity')->nullable(); // in Mbps
            $table->json('properties')->nullable();
            $table->timestamps();
            
            $table->unique(['source_node_id', 'target_node_id']);
            $table->index(['source_node_id', 'target_node_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
