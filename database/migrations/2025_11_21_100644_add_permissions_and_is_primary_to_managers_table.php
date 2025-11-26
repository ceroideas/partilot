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
        Schema::table('managers', function (Blueprint $table) {
            $table->boolean('is_primary')->default(false)->after('entity_id');
            $table->boolean('permission_sellers')->default(false)->after('is_primary');
            $table->boolean('permission_design')->default(false)->after('permission_sellers');
            $table->boolean('permission_statistics')->default(false)->after('permission_design');
            $table->boolean('permission_payments')->default(false)->after('permission_statistics');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('managers', function (Blueprint $table) {
            $table->dropColumn(['is_primary', 'permission_sellers', 'permission_design', 'permission_statistics', 'permission_payments']);
        });
    }
};
