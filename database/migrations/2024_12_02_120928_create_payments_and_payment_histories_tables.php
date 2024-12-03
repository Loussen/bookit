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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('reservation_id');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable()->unique();
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed',
                'refunded',
                'cancelled'
            ])->default('pending');
            $table->json('payment_details')->nullable();
            $table->string('error_message')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('transaction_id');
            $table->index(['reservation_id', 'status']);

            Schema::create('payment_histories', function (Blueprint $table) {
                $table->id();
                $table->integer('payment_id');
                $table->string('status');
                $table->string('message')->nullable();
                $table->json('metadata')->nullable();
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamps();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_histories');
        Schema::dropIfExists('payments');
    }
};
