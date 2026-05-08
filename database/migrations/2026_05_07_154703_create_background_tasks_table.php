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
        Schema::create('background_tasks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('type', 64);
            $table->string('status', 24)->default('pending')->index();
            $table->unsignedBigInteger('requested_by_user_id')->index();
            $table->unsignedBigInteger('entity_id')->nullable()->index();
            $table->unsignedBigInteger('administration_id')->nullable()->index();
            $table->unsignedBigInteger('set_id')->nullable()->index();
            $table->string('resource_key', 120)->nullable()->index();
            $table->string('task_hash', 64)->nullable()->index();
            $table->json('payload')->nullable();
            $table->unsignedInteger('progress_total')->default(0);
            $table->unsignedInteger('progress_done')->default(0);
            $table->unsignedTinyInteger('progress_percent')->default(0);
            $table->json('result_summary')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['requested_by_user_id', 'created_at']);
            $table->index(['type', 'status']);
            $table->index(['resource_key', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('background_tasks');
    }
};
