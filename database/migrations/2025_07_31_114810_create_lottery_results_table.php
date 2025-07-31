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
        Schema::create('lottery_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lottery_id')->constrained('lotteries')->onDelete('cascade');
            
            // Premio Especial
            $table->json('premio_especial')->nullable();
            
            // Primer Premio
            $table->json('primer_premio')->nullable();
            
            // Segundo Premio
            $table->json('segundo_premio')->nullable();
            
            // Arrays de premios
            $table->json('terceros_premios')->nullable();
            $table->json('cuartos_premios')->nullable();
            $table->json('quintos_premios')->nullable();
            
            // Arrays de extracciones
            $table->json('extracciones_cinco_cifras')->nullable();
            $table->json('extracciones_cuatro_cifras')->nullable();
            $table->json('extracciones_tres_cifras')->nullable();
            $table->json('extracciones_dos_cifras')->nullable();
            
            // Reintegros
            $table->json('reintegros')->nullable();
            
            // Metadatos
            $table->timestamp('results_date')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['lottery_id', 'results_date']);
            $table->index('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lottery_results');
    }
};
