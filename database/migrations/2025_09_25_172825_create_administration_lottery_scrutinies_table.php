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
            $table->foreignId('administration_id')->constrained()->onDelete('cascade');
            $table->foreignId('lottery_id')->constrained()->onDelete('cascade');
            $table->foreignId('lottery_result_id')->constrained()->onDelete('cascade');
            $table->timestamp('scrutiny_date');
            $table->boolean('is_scrutinized')->default(false);
            $table->boolean('is_saved')->default(false);
            $table->timestamp('saved_at')->nullable();
            $table->foreignId('saved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('scrutiny_summary')->nullable();
            $table->foreignId('scrutinized_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('comments')->nullable();
            $table->timestamps();
            
            // Índices únicos
            $table->unique(['administration_id', 'lottery_id'], 'admin_lottery_unique');
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