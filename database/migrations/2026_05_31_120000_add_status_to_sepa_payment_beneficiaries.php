<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sepa_payment_beneficiaries', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->after('remittance_info');
            $table->timestamp('paid_at')->nullable()->after('status');
        });

        // Sincronizar beneficiarios de órdenes ya marcadas como listo (datos previos al cambio)
        DB::table('sepa_payment_beneficiaries')
            ->join('sepa_payment_orders', 'sepa_payment_beneficiaries.sepa_payment_order_id', '=', 'sepa_payment_orders.id')
            ->where('sepa_payment_orders.status', 'listo')
            ->update([
                'sepa_payment_beneficiaries.status' => 'paid',
                'sepa_payment_beneficiaries.paid_at' => DB::raw('COALESCE(sepa_payment_beneficiaries.paid_at, sepa_payment_orders.updated_at)'),
            ]);
    }

    public function down(): void
    {
        Schema::table('sepa_payment_beneficiaries', function (Blueprint $table) {
            $table->dropColumn(['status', 'paid_at']);
        });
    }
};
