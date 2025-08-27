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
        Schema::table('scrutiny_entity_results', function (Blueprint $table) {
            $table->integer('winning_participations')->default(0)->after('total_winning');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scrutiny_entity_results', function (Blueprint $table) {
            $table->dropColumn('winning_participations');
        });
    }
};
