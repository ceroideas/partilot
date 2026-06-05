<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lottery_deadline_reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lottery_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('days_before');
            $table->string('channel', 32);
            $table->string('recipient', 191);
            $table->date('reminded_on');
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->unique(
                ['entity_id', 'lottery_id', 'days_before', 'channel', 'recipient', 'reminded_on'],
                'lottery_deadline_reminder_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lottery_deadline_reminder_logs');
    }
};
