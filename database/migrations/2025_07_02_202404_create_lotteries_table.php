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
        Schema::create('lotteries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Número/Nombre del sorteo
            $table->text('description')->nullable(); // Descripción del sorteo
            $table->date('draw_date')->nullable(); // Fecha del sorteo
            $table->time('draw_time')->nullable(); // Hora del sorteo
            $table->date('deadline_date')->nullable(); // Fecha límite
            $table->decimal('ticket_price', 10, 2)->nullable(); // Precio del décimo
            $table->integer('total_tickets')->nullable(); // Total de boletos disponibles
            $table->integer('sold_tickets')->default(0); // Boletos vendidos
            $table->string('prize_description')->nullable(); // Descripción del premio
            $table->decimal('prize_value', 10, 2)->nullable(); // Valor del premio
            $table->string('image')->nullable(); // Imagen del sorteo
            $table->integer('status')->default(1);
            $table->integer('lottery_type_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotteries');
    }
};
