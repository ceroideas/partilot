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
        Schema::table('devolution_details', function (Blueprint $table) {
            // Eliminar las columnas relacionadas con pagos
            $table->dropColumn(['amount', 'payment_method', 'from_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devolution_details', function (Blueprint $table) {
            // Restaurar las columnas si es necesario
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('payment_method')->nullable();
            $table->string('from_number')->nullable();
        });
    }
};