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
        Schema::create('devolutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->onDelete('cascade');
            $table->foreignId('lottery_id')->constrained('lotteries')->onDelete('cascade');
            $table->foreignId('seller_id')->nullable()->constrained('sellers')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Usuario que procesa la devolución
            $table->integer('total_participations')->default(0);
            $table->text('return_reason')->nullable();
            $table->date('devolution_date');
            $table->time('devolution_time');
            $table->enum('status', ['procesada', 'pendiente', 'cancelada'])->default('procesada');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Índices
            $table->index(['entity_id', 'lottery_id']);
            $table->index(['devolution_date']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devolutions');
    }
};
