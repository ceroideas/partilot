<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('print_configurations', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')->default(1)->after('id');
        });

        Schema::table('print_orders', function (Blueprint $table) {
            $table->foreignId('print_configuration_id')
                ->nullable()
                ->after('id')
                ->constrained('print_configurations')
                ->nullOnDelete();
        });

        if (Schema::hasTable('design_external_invitations') && ! Schema::hasColumn('design_external_invitations', 'print_configuration_id')) {
            Schema::table('design_external_invitations', function (Blueprint $table) {
                $table->foreignId('print_configuration_id')
                    ->nullable()
                    ->after('set_id')
                    ->constrained('print_configurations')
                    ->nullOnDelete();
            });
        }

        $defaultId = DB::table('print_configurations')->orderBy('id')->value('id');
        if ($defaultId) {
            DB::table('print_orders')
                ->whereNull('print_configuration_id')
                ->update(['print_configuration_id' => $defaultId]);

            if (Schema::hasColumn('design_external_invitations', 'print_configuration_id')) {
                DB::table('design_external_invitations')
                    ->whereNull('print_configuration_id')
                    ->update(['print_configuration_id' => $defaultId]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('design_external_invitations', 'print_configuration_id')) {
            Schema::table('design_external_invitations', function (Blueprint $table) {
                $table->dropConstrainedForeignId('print_configuration_id');
            });
        }

        Schema::table('print_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('print_configuration_id');
        });

        Schema::table('print_configurations', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
