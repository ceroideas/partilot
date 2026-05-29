<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('administrations', function (Blueprint $table) {
            $table->string('prepago_integration_name')->nullable()->after('account');
            $table->string('prepago_api_url', 500)->nullable()->after('prepago_integration_name');
            $table->string('prepago_auth_method', 32)->default('apikey')->after('prepago_api_url');
            $table->string('prepago_api_prefix', 32)->nullable()->after('prepago_auth_method');
            $table->text('prepago_api_key')->nullable()->after('prepago_api_prefix');
            $table->boolean('prepago_use_partilot_default')->default(false)->after('prepago_api_key');
            $table->boolean('prepago_integration_enabled')->default(true)->after('prepago_use_partilot_default');
        });
    }

    public function down(): void
    {
        Schema::table('administrations', function (Blueprint $table) {
            $table->dropColumn([
                'prepago_integration_name',
                'prepago_api_url',
                'prepago_auth_method',
                'prepago_api_prefix',
                'prepago_api_key',
                'prepago_use_partilot_default',
                'prepago_integration_enabled',
            ]);
        });
    }
};
