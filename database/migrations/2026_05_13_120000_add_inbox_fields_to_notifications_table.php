<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (! Schema::hasColumn('notifications', 'recipient_user_id')) {
                $table->foreignId('recipient_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            }
            if (! Schema::hasColumn('notifications', 'kind')) {
                $table->string('kind', 64)->nullable()->index();
            }
            if (! Schema::hasColumn('notifications', 'meta')) {
                $table->json('meta')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'meta')) {
                $table->dropColumn('meta');
            }
            if (Schema::hasColumn('notifications', 'kind')) {
                $table->dropColumn('kind');
            }
            if (Schema::hasColumn('notifications', 'recipient_user_id')) {
                $table->dropForeign(['recipient_user_id']);
                $table->dropColumn('recipient_user_id');
            }
        });
    }
};
