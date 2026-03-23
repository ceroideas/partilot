<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Administration;
use App\Models\User;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('panel_login_username', 64)->nullable()->unique()->after('panel_account_id');
        });

        Schema::create('panel_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token_hash', 64);
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
            $table->index(['token_hash', 'expires_at']);
        });

        $this->backfillPanelLoginUsernames();
    }

    public function down(): void
    {
        Schema::dropIfExists('panel_access_tokens');

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['panel_login_username']);
            $table->dropColumn('panel_login_username');
        });
    }

    private function backfillPanelLoginUsernames(): void
    {
        User::query()
            ->where('panel_account_type', 'administration')
            ->whereNotNull('panel_account_id')
            ->orderBy('id')
            ->each(function (User $user) {
                $adm = Administration::query()->find($user->panel_account_id);
                if (! $adm) {
                    return;
                }
                $base = Administration::panelLoginUsernameFromParts($adm->receiving, $adm->admin_number);
                $username = Administration::ensureUniquePanelLoginUsername($base, $user->id);
                DB::table('users')->where('id', $user->id)->update([
                    'panel_login_username' => $username,
                    'updated_at' => now(),
                ]);
            });
    }
};
