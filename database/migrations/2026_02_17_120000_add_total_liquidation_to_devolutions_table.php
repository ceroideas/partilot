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
        Schema::table('devolutions', function (Blueprint $table) {
            $table->decimal('total_liquidation', 12, 2)->nullable()->after('total_participations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devolutions', function (Blueprint $table) {
            $table->dropColumn('total_liquidation');
        });
    }
};
