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
        // Eliminar las tablas de escrutinio actuales
        Schema::dropIfExists('scrutiny_entity_results');
        Schema::dropIfExists('administration_lottery_scrutinies');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recrear las tablas si es necesario hacer rollback
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
        });

        Schema::create('scrutiny_entity_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('administration_lottery_scrutiny_id')->constrained()->onDelete('cascade');
            $table->foreignId('entity_id')->constrained()->onDelete('cascade');
            $table->json('reserved_numbers')->nullable();
            $table->integer('total_reserved')->default(0);
            $table->integer('total_issued')->default(0);
            $table->integer('total_sold')->default(0);
            $table->integer('total_returned')->default(0);
            $table->integer('total_non_winning')->default(0);
            $table->integer('total_winning')->default(0);
            $table->integer('winning_participations')->default(0);
            $table->decimal('total_prize_amount', 15, 2)->default(0);
            $table->decimal('prize_per_participation', 15, 2)->default(0);
            $table->json('prize_breakdown')->nullable();
            $table->timestamps();
        });
    }
};