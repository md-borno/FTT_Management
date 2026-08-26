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
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('customer_id')->unique();
            $table->foreignId('service_plan_id')->constrained();
            $table->enum('status', ['active', 'inactive', 'suspended', 'pending', 'cancelled'])->default('pending');
            $table->json('preferences')->nullable();
            $table->json('billing_info')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->integer('data_usage')->default(0); // in MB
            $table->boolean('is_priority')->default(false);
            $table->timestamps();
            
            $table->index(['status', 'activated_at']);
            $table->index('customer_id');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscribers');
    }
};
