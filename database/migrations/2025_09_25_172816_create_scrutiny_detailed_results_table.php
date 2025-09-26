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
        Schema::create('scrutiny_detailed_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scrutiny_id')->constrained('administration_lottery_scrutinies')->onDelete('cascade');
            $table->foreignId('entity_id')->constrained()->onDelete('cascade');
            $table->string('winning_number', 5);
            $table->foreignId('set_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('premio_por_decimo', 15, 2)->default(0);
            $table->decimal('premio_por_participacion', 15, 2)->default(0);
            $table->integer('total_decimos')->default(0);
            $table->integer('total_participations')->default(0);
            $table->decimal('premio_total', 15, 2)->default(0);
            $table->json('winning_categories')->nullable();
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['scrutiny_id', 'entity_id']);
            $table->index(['winning_number']);
            $table->index(['set_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scrutiny_detailed_results');
    }
};