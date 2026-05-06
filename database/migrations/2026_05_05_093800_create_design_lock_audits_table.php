<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_lock_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('set_id')->index();
            $table->unsignedBigInteger('entity_id')->nullable()->index();
            $table->unsignedBigInteger('design_format_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('action', 80);
            $table->string('message', 500)->nullable();
            $table->unsignedInteger('assigned_count')->default(0);
            $table->unsignedInteger('status_locked_count')->default(0);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_lock_audits');
    }
};

