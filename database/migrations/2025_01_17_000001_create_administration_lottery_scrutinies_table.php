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
        Schema::create('administration_lottery_scrutinies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('administration_id')->constrained('administrations')->onDelete('cascade');
            $table->foreignId('lottery_id')->constrained('lotteries')->onDelete('cascade');
            $table->foreignId('lottery_result_id')->nullable()->constrained('lottery_results')->onDelete('cascade');
            
            // Metadatos del escrutinio
            $table->timestamp('scrutiny_date')->nullable();
            $table->boolean('is_scrutinized')->default(false);
            $table->json('scrutiny_summary')->nullable(); // Resumen: total premiadas, no premiadas, importe total
            
            // Información del usuario que realizó el escrutinio
            $table->foreignId('scrutinized_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('comments')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['administration_id', 'lottery_id']);
            $table->index(['lottery_id', 'is_scrutinized']);
            $table->unique(['administration_id', 'lottery_id']); // Un escrutinio por administración por sorteo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administration_lottery_scrutinies');
    }
};
