<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modificar el enum para agregar 'asignada'
        DB::statement("ALTER TABLE participations MODIFY COLUMN status ENUM('disponible', 'reservada', 'vendida', 'devuelta', 'anulada', 'perdida', 'asignada') DEFAULT 'disponible'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir el enum a su estado original
        DB::statement("ALTER TABLE participations MODIFY COLUMN status ENUM('disponible', 'reservada', 'vendida', 'devuelta', 'anulada', 'perdida') DEFAULT 'disponible'");
    }
};
