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
        Schema::create('participation_activity_logs', function (Blueprint $table) {
            $table->id();
            
            // Relación con la participación
            $table->foreignId('participation_id')->constrained()->onDelete('cascade');
            
            // Tipo de actividad
            $table->enum('activity_type', [
                'created',                    // Cuando se crea la participación
                'assigned',                   // Cuando se asigna a un vendedor
                'returned_by_seller',         // Cuando el vendedor devuelve la participación
                'sold',                       // Cuando se vende
                'returned_to_administration', // Cuando la entidad devuelve a la administración
                'status_changed',             // Cuando cambia de estado
                'cancelled',                  // Cuando se anula
                'modified'                    // Cuando se modifica
            ]);
            
            // Usuario que realizó la acción (puede ser null en creaciones automáticas)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Vendedor involucrado en la actividad (si aplica)
            $table->foreignId('seller_id')->nullable()->constrained()->onDelete('set null');
            
            // Entidad involucrada
            $table->foreignId('entity_id')->nullable()->constrained()->onDelete('set null');
            
            // Estado anterior y nuevo (para cambios de estado)
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            
            // Vendedor anterior y nuevo (para cambios de asignación)
            $table->foreignId('old_seller_id')->nullable()->constrained('sellers')->onDelete('set null');
            $table->foreignId('new_seller_id')->nullable()->constrained('sellers')->onDelete('set null');
            
            // Descripción de la actividad
            $table->text('description')->nullable();
            
            // Datos adicionales en formato JSON (cambios completos, datos de venta, etc.)
            $table->json('metadata')->nullable();
            
            // Dirección IP y User Agent para auditoría
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['participation_id', 'created_at']);
            $table->index(['activity_type']);
            $table->index(['user_id']);
            $table->index(['seller_id']);
            $table->index(['entity_id']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participation_activity_logs');
    }
};
