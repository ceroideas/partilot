<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Añade el valor 'anular' al ENUM action de devolution_details.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE devolution_details MODIFY COLUMN action ENUM('devolver', 'vender', 'devolver_vendedor', 'anular') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE devolution_details MODIFY COLUMN action ENUM('devolver', 'vender', 'devolver_vendedor') NOT NULL");
    }
};
