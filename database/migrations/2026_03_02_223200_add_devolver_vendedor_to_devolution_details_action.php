<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Añade el valor 'devolver_vendedor' al ENUM action de devolution_details.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE devolution_details MODIFY COLUMN action ENUM('devolver', 'vender', 'devolver_vendedor') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No se pueden eliminar filas con 'devolver_vendedor' desde la migración de forma segura;
        // revierte el enum a los valores originales (las filas con devolver_vendedor quedarían inválidas)
        DB::statement("ALTER TABLE devolution_details MODIFY COLUMN action ENUM('devolver', 'vender') NOT NULL");
    }
};
