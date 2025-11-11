<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('super_admin')->after('status')->index();
        });

        // Asignar roles existentes según relaciones actuales
        $administrationUserIds = DB::table('managers')
            ->whereNotNull('administration_id')
            ->pluck('user_id')
            ->unique()
            ->toArray();

        if (!empty($administrationUserIds)) {
            DB::table('users')
                ->whereIn('id', $administrationUserIds)
                ->update(['role' => 'administration']);
        }

        $entityUserIds = DB::table('managers')
            ->whereNotNull('entity_id')
            ->pluck('user_id')
            ->unique()
            ->toArray();

        if (!empty($entityUserIds)) {
            DB::table('users')
                ->whereIn('id', $entityUserIds)
                ->update(['role' => 'entity']);
        }

        $sellerUserIds = DB::table('sellers')
            ->where('user_id', '>', 0)
            ->pluck('user_id')
            ->unique()
            ->toArray();

        if (!empty($sellerUserIds)) {
            $idsToUpdate = array_diff(
                $sellerUserIds,
                $administrationUserIds,
                $entityUserIds
            );

            if (!empty($idsToUpdate)) {
                DB::table('users')
                    ->whereIn('id', $idsToUpdate)
                    ->update(['role' => 'seller']);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};

