<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lottery_deadline_closure_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lottery_id')->constrained()->cascadeOnDelete();
            $table->date('effective_deadline');
            $table->foreignId('devolution_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('participations_sold')->default(0);
            $table->unsignedInteger('participations_returned_digital')->default(0);
            $table->decimal('total_liquidation', 12, 2)->default(0);
            $table->string('status', 32);
            $table->text('message')->nullable();
            $table->timestamp('processed_at');
            $table->timestamps();

            $table->index(['entity_id', 'lottery_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lottery_deadline_closure_logs');
    }
};
