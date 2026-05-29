<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entities', function (Blueprint $table) {
            if (! Schema::hasColumn('entities', 'billing_iban')) {
                $table->string('billing_iban', 34)->nullable()->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('entities', function (Blueprint $table) {
            if (Schema::hasColumn('entities', 'billing_iban')) {
                $table->dropColumn('billing_iban');
            }
        });
    }
};
