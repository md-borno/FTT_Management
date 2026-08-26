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
        Schema::create('alarms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('severity', ['critical', 'major', 'minor', 'warning'])->default('warning');
            $table->string('source');
            $table->foreignId('device_id')->constrained();
            $table->text('description');
            $table->text('resolution')->nullable();
            $table->json('parameters')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('acknowledged_by')->nullable()->constrained('users');
            $table->foreignId('resolved_by')->nullable()->constrained('users');
            $table->boolean('is_auto_resolved')->default(false);
            $table->timestamps();
            
            $table->index(['severity', 'occurred_at']);
            $table->index(['device_id', 'occurred_at']);
            $table->index('acknowledged_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alarms');
    }
};
