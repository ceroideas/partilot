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
        Schema::table('sepa_payment_beneficiaries', function (Blueprint $table) {
            $table->foreignId('participation_collection_id')->nullable()->after('remittance_info')
                ->constrained('participation_collections')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sepa_payment_beneficiaries', function (Blueprint $table) {
            $table->dropForeign(['participation_collection_id']);
        });
    }
};
