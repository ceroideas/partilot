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
        Schema::create('sepa_payment_beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sepa_payment_order_id')->constrained('sepa_payment_orders')->onDelete('cascade');
            $table->string('end_to_end_id')->unique(); // PmtId EndToEndId
            $table->decimal('amount', 12, 2); // Amt InstdAmt __text
            $table->string('currency', 3)->default('EUR'); // Amt InstdAmt _Ccy
            // Datos del acreedor (beneficiario)
            $table->string('creditor_name'); // Cdtr Nm
            $table->string('creditor_nif_cif')->nullable(); // Cdtr Id PrvtId Othr Id
            $table->string('creditor_iban'); // CdtrAcct Id IBAN
            $table->string('purpose_code')->default('CASH'); // Purp Cd
            $table->text('remittance_info')->nullable(); // RmtInf Ustrd
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sepa_payment_beneficiaries');
    }
};
