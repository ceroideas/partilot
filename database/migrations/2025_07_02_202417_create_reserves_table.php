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
        Schema::create('reserves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->onDelete('cascade');
            $table->foreignId('lottery_id')->constrained('lotteries')->onDelete('cascade');
            $table->json('reservation_numbers'); // Array de números reservados
            $table->decimal('total_amount', 10, 2);
            $table->integer('total_tickets');
            $table->decimal('reservation_amount', 10, 2); // Importe a reservar
            $table->integer('reservation_tickets'); // Cantidad de décimos
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->timestamp('reservation_date');
            $table->timestamp('expiration_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserves');
    }
};
