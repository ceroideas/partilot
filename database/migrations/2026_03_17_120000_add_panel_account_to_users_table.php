<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('panel_account_type', 32)->nullable()->after('role');
            $table->unsignedBigInteger('panel_account_id')->nullable()->after('panel_account_type');
            $table->index(['panel_account_type', 'panel_account_id'], 'users_panel_account_idx');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_panel_account_idx');
            $table->dropColumn(['panel_account_type', 'panel_account_id']);
        });
    }
};
