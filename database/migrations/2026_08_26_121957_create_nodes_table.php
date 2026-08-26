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
       Schema::create('nodes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // OLT, ONT, splitter, switch, router
            $table->foreignId('device_id')->nullable()->constrained();
            $table->foreignId('location_id')->constrained();
            $table->float('x_position')->nullable();
            $table->float('y_position')->nullable();
            $table->json('properties')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('type');
            $table->index(['location_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nodes');
    }
};
