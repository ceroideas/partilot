<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * TIMESTAMP en MySQL puede llevar ON UPDATE CURRENT_TIMESTAMP y
     * sobrescribir valid_until al incrementar buyer_sms_sent_count.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE pending_digital_sales MODIFY valid_until DATETIME NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE pending_digital_sales MODIFY valid_until TIMESTAMP NOT NULL');
    }
};
