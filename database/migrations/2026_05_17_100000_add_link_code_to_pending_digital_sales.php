<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_digital_sales', function (Blueprint $table) {
            $table->string('link_code', 12)->nullable()->unique()->after('registration_token');
        });
    }

    public function down(): void
    {
        Schema::table('pending_digital_sales', function (Blueprint $table) {
            $table->dropUnique(['link_code']);
            $table->dropColumn('link_code');
        });
    }
};
