<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('print_configurations', function (Blueprint $table) {
            $table->string('stripe_publishable_key', 255)->nullable()->after('bank_account');
            $table->text('stripe_secret_key')->nullable()->after('stripe_publishable_key');
            $table->text('stripe_webhook_secret')->nullable()->after('stripe_secret_key');
        });
    }

    public function down(): void
    {
        Schema::table('print_configurations', function (Blueprint $table) {
            $table->dropColumn(['stripe_publishable_key', 'stripe_secret_key', 'stripe_webhook_secret']);
        });
    }
};

