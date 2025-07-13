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
        Schema::create('sets', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('entity_id')->constrained('entities')->onDelete('cascade');
            $table->foreignId('reserve_id')->constrained('reserves')->onDelete('cascade');
            
            // Información del set
            $table->string('set_name');
            $table->text('set_description')->nullable();
            
            // Configuración de participaciones
            $table->integer('total_participations');
            $table->decimal('participation_price', 10, 2);
            $table->decimal('total_amount', 12, 2);
            
            // Campos adicionales para tipos de participaciones
            $table->decimal('played_amount', 10, 2)->nullable(); // Importe jugado por número
            $table->decimal('donation_amount', 10, 2)->nullable(); // Importe donativo
            $table->decimal('total_participation_amount', 10, 2)->nullable(); // Importe total participación
            $table->integer('physical_participations')->default(0); // Participaciones físicas
            $table->integer('digital_participations')->default(0); // Participaciones digitales
            $table->date('deadline_date')->nullable(); // Fecha límite
            
            // Estado (0=inactivo, 1=activo, 2=pausado)
            $table->tinyInteger('status')->default(1);
            
            // Timestamps
            $table->timestamps();
            
            // Índices para mejorar rendimiento
            $table->index(['entity_id', 'status']);
            $table->index(['reserve_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sets');
    }
};
