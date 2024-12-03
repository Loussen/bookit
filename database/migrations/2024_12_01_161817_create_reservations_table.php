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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->integer('room_id');
            $table->string('client_telegram_id');
            $table->string('client_name');
            $table->string('client_phone')->nullable();
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('guest_count');
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['pending', 'approved', 'paid', 'rejected', 'cancelled']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
