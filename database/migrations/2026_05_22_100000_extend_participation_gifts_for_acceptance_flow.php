<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participation_gifts', function (Blueprint $table) {
            $table->dropForeign(['to_user_id']);
        });

        Schema::table('participation_gifts', function (Blueprint $table) {
            $table->string('to_email')->nullable()->after('to_user_id');
            $table->string('status', 20)->default('pending')->after('to_email');
            $table->text('message')->nullable()->after('status');
            $table->string('claim_token', 64)->nullable()->unique()->after('message');
            $table->timestamp('accepted_at')->nullable()->after('claim_token');
            $table->timestamp('rejected_at')->nullable()->after('accepted_at');
            $table->unsignedBigInteger('to_user_id')->nullable()->change();
        });

        Schema::table('participation_gifts', function (Blueprint $table) {
            $table->foreign('to_user_id')->references('id')->on('users')->nullOnDelete();
        });

        // Regalos ya existentes: se consideran aceptados (comportamiento anterior)
        DB::table('participation_gifts')->update([
            'status' => 'accepted',
            'accepted_at' => DB::raw('COALESCE(accepted_at, created_at)'),
        ]);
    }

    public function down(): void
    {
        Schema::table('participation_gifts', function (Blueprint $table) {
            $table->dropForeign(['to_user_id']);
        });

        Schema::table('participation_gifts', function (Blueprint $table) {
            $table->dropColumn(['to_email', 'status', 'message', 'claim_token', 'accepted_at', 'rejected_at']);
            $table->unsignedBigInteger('to_user_id')->nullable(false)->change();
        });

        Schema::table('participation_gifts', function (Blueprint $table) {
            $table->foreign('to_user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
