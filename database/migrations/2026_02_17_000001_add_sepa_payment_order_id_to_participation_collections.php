<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participation_collections', function (Blueprint $table) {
            $table->foreignId('sepa_payment_order_id')->nullable()->after('collected_at')->constrained('sepa_payment_orders')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('participation_collections', function (Blueprint $table) {
            $table->dropForeign(['sepa_payment_order_id']);
        });
    }
};
