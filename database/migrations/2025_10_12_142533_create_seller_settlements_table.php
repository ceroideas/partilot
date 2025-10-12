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
        Schema::create('seller_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('sellers')->onDelete('cascade');
            $table->foreignId('lottery_id')->constrained('lotteries')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('total_amount', 10, 2)->default(0); // Total a liquidar
            $table->decimal('paid_amount', 10, 2)->default(0); // Total pagado
            $table->decimal('pending_amount', 10, 2)->default(0); // Pendiente
            $table->integer('total_participations')->default(0); // Total participaciones del vendedor
            $table->decimal('calculated_participations', 10, 2)->default(0); // Participaciones liquidadas calculadas
            $table->date('settlement_date'); // Fecha de liquidación
            $table->time('settlement_time'); // Hora de liquidación
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('seller_id');
            $table->index('lottery_id');
            $table->index('settlement_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_settlements');
    }
};
