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
        Schema::create('devolution_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devolution_id')->constrained('devolutions')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['efectivo', 'bizum', 'transferencia', 'otro']);
            $table->string('from_number')->nullable(); // Número de origen del pago
            $table->text('notes')->nullable(); // Notas adicionales del pago
            $table->timestamp('payment_date')->nullable(); // Fecha específica del pago
            $table->timestamps();

            // Índices
            $table->index(['devolution_id']);
            $table->index(['payment_method']);
            $table->index(['payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devolution_payments');
    }
};