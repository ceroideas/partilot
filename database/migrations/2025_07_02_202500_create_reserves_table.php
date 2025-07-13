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
        Schema::create('reserves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->onDelete('cascade');
            $table->foreignId('lottery_id')->constrained('lotteries')->onDelete('cascade');
            $table->string('reservation_numbers'); // JSON array de números reservados
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->text('customer_address')->nullable();
            $table->string('customer_nif_cif')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->integer('total_tickets');
            $table->tinyInteger('status')->default(0); // 0=pending, 1=confirmed, 2=cancelled, 3=completed
            $table->text('notes')->nullable();
            $table->timestamp('reservation_date');
            $table->timestamp('expiration_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserves');
    }
}; 