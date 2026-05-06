<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('print_orders', function (Blueprint $table) {
            $table->string('payment_provider', 30)->nullable()->after('status');
            $table->string('payment_intent_id', 120)->nullable()->unique()->after('payment_provider');
            $table->string('payment_status', 30)->default('pending')->after('payment_intent_id');
            $table->timestamp('paid_at')->nullable()->after('sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('print_orders', function (Blueprint $table) {
            $table->dropColumn(['payment_provider', 'payment_intent_id', 'payment_status', 'paid_at']);
        });
    }
};

