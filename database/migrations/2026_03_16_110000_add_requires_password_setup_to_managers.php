<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('managers', function (Blueprint $table) {
            if (! Schema::hasColumn('managers', 'requires_password_setup')) {
                $table->boolean('requires_password_setup')
                    ->default(false)
                    ->after('confirmation_sent_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('managers', function (Blueprint $table) {
            if (Schema::hasColumn('managers', 'requires_password_setup')) {
                $table->dropColumn('requires_password_setup');
            }
        });
    }
};
