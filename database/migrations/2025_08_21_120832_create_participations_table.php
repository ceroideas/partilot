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
        Schema::create('participations', function (Blueprint $table) {
            $table->id();
            
            // Relaciones principales
            $table->foreignId('entity_id')->constrained()->onDelete('cascade');
            $table->foreignId('set_id')->constrained()->onDelete('cascade');
            $table->foreignId('design_format_id')->constrained()->onDelete('cascade');
            
            // Información de la participación
            $table->integer('participation_number'); // Número secuencial dentro del set
            $table->string('participation_code')->unique(); // Código único: {set_number}/{participation_number}
            $table->integer('book_number'); // Número del taco al que pertenece
            
            // Estado de la participación
            $table->enum('status', [
                'disponible',      // Disponible para venta
                'reservada',       // Reservada temporalmente
                'vendida',         // Vendida
                'devuelta',        // Devuelta por el vendedor
                'anulada',         // Anulada
                'perdida'          // Perdida o extraviada
            ])->default('disponible');
            
            // Información de venta
            $table->foreignId('seller_id')->nullable()->constrained()->onDelete('set null');
            $table->date('sale_date')->nullable();
            $table->time('sale_time')->nullable();
            $table->decimal('sale_amount', 10, 2)->nullable(); // Importe de la venta
            
            // Información del comprador
            $table->string('buyer_name')->nullable();
            $table->string('buyer_phone')->nullable();
            $table->string('buyer_email')->nullable();
            $table->string('buyer_nif')->nullable();
            
            // Información de devolución
            $table->date('return_date')->nullable();
            $table->time('return_time')->nullable();
            $table->text('return_reason')->nullable();
            $table->foreignId('returned_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Información de anulación
            $table->date('cancellation_date')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Información adicional
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Datos adicionales en formato JSON
            
            // Timestamps
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['entity_id', 'set_id']);
            $table->index(['set_id', 'participation_number']);
            $table->index(['book_number', 'set_id']);
            $table->index(['status']);
            $table->index(['seller_id']);
            $table->index(['sale_date']);
            
            // Restricciones únicas
            $table->unique(['set_id', 'participation_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participations');
    }
};
