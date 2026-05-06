<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 30)->unique();
            $table->unsignedBigInteger('design_format_id')->index();
            $table->unsignedBigInteger('set_id')->index();
            $table->unsignedBigInteger('entity_id')->index();
            $table->unsignedBigInteger('lottery_id')->nullable()->index();
            $table->unsignedBigInteger('created_by_user_id')->nullable()->index();
            $table->string('status', 40)->default('pendiente_revision')->index();
            $table->string('print_size', 30)->nullable();
            $table->unsignedInteger('participations_per_book')->nullable();
            $table->string('back_mode', 10)->nullable();
            $table->decimal('quoted_amount', 10, 2)->default(0);
            $table->json('quote_breakdown')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_orders');
    }
};

