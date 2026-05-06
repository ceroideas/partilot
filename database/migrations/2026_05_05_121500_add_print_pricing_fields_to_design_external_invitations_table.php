<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('design_external_invitations', function (Blueprint $table) {
            $table->string('print_size', 30)->nullable()->after('comment');
            $table->unsignedInteger('participations_per_book')->nullable()->after('print_size');
            $table->string('back_mode', 10)->nullable()->after('participations_per_book');
            $table->decimal('quoted_amount', 10, 2)->nullable()->after('back_mode');
            $table->json('quote_breakdown')->nullable()->after('quoted_amount');
        });
    }

    public function down(): void
    {
        Schema::table('design_external_invitations', function (Blueprint $table) {
            $table->dropColumn([
                'print_size',
                'participations_per_book',
                'back_mode',
                'quoted_amount',
                'quote_breakdown',
            ]);
        });
    }
};

