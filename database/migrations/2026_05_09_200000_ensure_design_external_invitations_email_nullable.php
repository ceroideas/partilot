<?php

/**
 * El paso 1 del flujo externo crea la invitación sin email (se rellena en paso 2 o queda null en modo PARTILOT).
 * Si la migración anterior que usaba ->change() no se aplicó (p. ej. sin doctrine/dbal), email sigue siendo NOT NULL y falla el INSERT.
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('design_external_invitations')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE design_external_invitations MODIFY email VARCHAR(255) NULL');
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE design_external_invitations ALTER COLUMN email DROP NOT NULL');

            return;
        }

        // sqlite u otros: intentar el cambio estándar
        Schema::table('design_external_invitations', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('design_external_invitations')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE design_external_invitations MODIFY email VARCHAR(255) NOT NULL');

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE design_external_invitations ALTER COLUMN email SET NOT NULL');

            return;
        }

        Schema::table('design_external_invitations', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->string('email')->nullable(false)->change();
        });
    }
};
