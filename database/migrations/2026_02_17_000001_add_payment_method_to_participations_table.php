<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tarea 3 QR: método de pago por venta – evitar propagación.
     * Guardar payment_method en cada participación para que el historial muestre el correcto.
     */
    public function up(): void
    {
        Schema::table('participations', function (Blueprint $table) {
            $table->string('payment_method', 50)->nullable()->after('sale_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participations', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
    }
};
