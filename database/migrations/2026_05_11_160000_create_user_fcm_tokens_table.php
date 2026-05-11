<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('user_fcm_tokens')) {
            Schema::create('user_fcm_tokens', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->text('token');
                /** android | ios | web (PWA / messaging web) */
                $table->string('platform', 32)->default('android');
                $table->timestamps();

                $table->unique('token');
                $table->index(['user_id', 'platform']);
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'fcm_token')) {
            $rows = DB::table('users')->whereNotNull('fcm_token')->where('fcm_token', '!=', '')->get(['id', 'fcm_token']);
            foreach ($rows as $row) {
                DB::table('user_fcm_tokens')->insertOrIgnore([
                    'user_id' => $row->id,
                    'token' => $row->fcm_token,
                    'platform' => 'android',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('fcm_token');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('fcm_token')->nullable()->after('remember_token');
        });

        $tokens = DB::table('user_fcm_tokens')->orderBy('id')->get(['user_id', 'token']);
        $seenUser = [];
        foreach ($tokens as $t) {
            if (! isset($seenUser[$t->user_id])) {
                DB::table('users')->where('id', $t->user_id)->update(['fcm_token' => $t->token]);
                $seenUser[$t->user_id] = true;
            }
        }

        Schema::dropIfExists('user_fcm_tokens');
    }
};
