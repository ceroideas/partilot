<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('administration_lottery_scrutinies', function (Blueprint $table) {
            $table->boolean('is_saved')->default(false)->after('is_scrutinized')
                  ->comment('Indica si el escrutinio ha sido guardado definitivamente');
            $table->timestamp('saved_at')->nullable()->after('is_saved')
                  ->comment('Fecha y hora cuando se guardó el escrutinio');
            $table->foreignId('saved_by')->nullable()->constrained('users')->onDelete('set null')->after('saved_at')
                  ->comment('Usuario que guardó el escrutinio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('administration_lottery_scrutinies', function (Blueprint $table) {
            $table->dropForeign(['saved_by']);
            $table->dropColumn(['is_saved', 'saved_at', 'saved_by']);
        });
    }
};
