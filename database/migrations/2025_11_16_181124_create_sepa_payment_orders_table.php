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
        Schema::create('sepa_payment_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('administration_id')->nullable()->constrained('administrations')->onDelete('cascade');
            $table->string('message_id')->unique(); // MsgId
            $table->timestamp('creation_date'); // CreDtTm
            $table->date('execution_date'); // ReqdExctnDt
            $table->integer('number_of_transactions'); // NbOfTxs
            $table->decimal('control_sum', 12, 2); // CtrlSum
            $table->string('payment_info_id'); // PmtInfId
            $table->boolean('batch_booking')->default(false); // BtchBookg
            $table->string('charge_bearer')->default('SLEV'); // ChrgBr
            // Datos del deudor (pagador)
            $table->string('debtor_name'); // Dbtr Nm
            $table->string('debtor_nif_cif')->nullable(); // Dbtr Id PrvtId Othr Id
            $table->string('debtor_iban'); // DbtrAcct Id IBAN
            $table->string('debtor_address')->nullable(); // Dbtr PstlAdr
            $table->string('xml_filename')->nullable(); // Nombre del archivo XML generado
            $table->string('status')->default('draft'); // draft, generated, uploaded
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sepa_payment_orders');
    }
};
