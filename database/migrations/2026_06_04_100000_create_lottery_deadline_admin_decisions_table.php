<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lottery_deadline_admin_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lottery_id')->constrained()->cascadeOnDelete();
            $table->string('decision', 32);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['entity_id', 'lottery_id'], 'lottery_deadline_admin_decision_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lottery_deadline_admin_decisions');
    }
};
