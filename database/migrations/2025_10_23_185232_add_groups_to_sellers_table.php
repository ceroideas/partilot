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
        Schema::table('sellers', function (Blueprint $table) {
            $table->string('group_name')->nullable()->after('comment');
            $table->string('group_color', 7)->nullable()->after('group_name'); // Para colores hex
            $table->integer('group_priority')->default(0)->after('group_color'); // Para ordenar grupos
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn(['group_name', 'group_color', 'group_priority']);
        });
    }
};