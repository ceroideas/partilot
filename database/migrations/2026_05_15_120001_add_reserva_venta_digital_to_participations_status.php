<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE participations MODIFY COLUMN status ENUM(
            'disponible',
            'reservada',
            'vendida',
            'devuelta',
            'anulada',
            'perdida',
            'asignada',
            'pagada',
            'reserva_venta_digital'
        ) DEFAULT 'disponible'");
    }

    public function down(): void
    {
        DB::table('participations')
            ->where('status', 'reserva_venta_digital')
            ->update(['status' => 'disponible']);

        DB::statement("ALTER TABLE participations MODIFY COLUMN status ENUM(
            'disponible',
            'reservada',
            'vendida',
            'devuelta',
            'anulada',
            'perdida',
            'asignada',
            'pagada'
        ) DEFAULT 'disponible'");
    }
};
