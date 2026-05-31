<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participation_collections', function (Blueprint $table) {
            $table->string('status', 30)->default('pending_verification')->after('importe_total');
            $table->string('confirmation_token', 64)->nullable()->unique()->after('status');
            $table->timestamp('confirmation_sent_at')->nullable()->after('confirmation_token');
            $table->timestamp('verified_at')->nullable()->after('confirmation_sent_at');
            $table->timestamp('expires_at')->nullable()->after('verified_at');
        });

        Schema::table('participation_collections', function (Blueprint $table) {
            $table->timestamp('collected_at')->nullable()->change();
        });

        // Solicitudes existentes (ya procesadas) → verificadas
        DB::table('participation_collections')
            ->whereNotNull('collected_at')
            ->update([
                'status' => 'verified',
                'verified_at' => DB::raw('collected_at'),
            ]);
    }

    public function down(): void
    {
        Schema::table('participation_collections', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'confirmation_token',
                'confirmation_sent_at',
                'verified_at',
                'expires_at',
            ]);
        });
    }
};
