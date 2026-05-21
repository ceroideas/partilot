<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_digital_sales', function (Blueprint $table) {
            $table->unsignedTinyInteger('buyer_sms_sent_count')->default(0)->after('link_code');
        });
    }

    public function down(): void
    {
        Schema::table('pending_digital_sales', function (Blueprint $table) {
            $table->dropColumn('buyer_sms_sent_count');
        });
    }
};
