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
        Schema::create('seller_settlement_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_settlement_id')->constrained('seller_settlements')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // efectivo, bizum, transferencia
            $table->text('notes')->nullable();
            $table->timestamp('payment_date')->useCurrent();
            $table->timestamps();

            $table->index('seller_settlement_id');
            $table->index('payment_method');
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_settlement_payments');
    }
};
