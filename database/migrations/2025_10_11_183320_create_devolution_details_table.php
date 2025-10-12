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
        Schema::create('devolution_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devolution_id')->constrained('devolutions')->onDelete('cascade');
            $table->foreignId('participation_id')->constrained('participations')->onDelete('cascade');
            $table->enum('action', ['devolver', 'vender']); // Qué acción se realizó con la participación
            $table->decimal('amount', 10, 2)->nullable(); // Monto si es venta
            $table->string('payment_method')->nullable(); // Método de pago si es venta
            $table->string('from_number')->nullable(); // Número desde el cual se paga
            $table->timestamps();

            // Índices
            $table->index(['devolution_id']);
            $table->index(['participation_id']);
            $table->index(['action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devolution_details');
    }
};
