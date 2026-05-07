<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('managers', function (Blueprint $table) {
            if (! Schema::hasColumn('managers', 'pending_primary')) {
                $table->boolean('pending_primary')->default(false)->after('is_primary');
            }
        });
    }

    public function down(): void
    {
        Schema::table('managers', function (Blueprint $table) {
            if (Schema::hasColumn('managers', 'pending_primary')) {
                $table->dropColumn('pending_primary');
            }
        });
    }
};

