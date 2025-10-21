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
        // 1. Crear tabla pivote entity_seller (si no existe ya)
        if (!Schema::hasTable('entity_seller')) {
            Schema::create('entity_seller', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained()->onDelete('cascade');
            $table->foreignId('seller_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
                // Evitar duplicados
                $table->unique(['entity_id', 'seller_id']);
            });
        }

        // 2. Migrar datos existentes de sellers.entity_id a la tabla pivote
        $this->migrateExistingData();

        // 3. Eliminar la columna entity_id de la tabla sellers (si existe)
        try {
            // Intentar eliminar foreign key si existe
            try {
                Schema::table('sellers', function (Blueprint $table) {
                    $table->dropForeign(['entity_id']);
                });
            } catch (\Exception $e) {
                // Foreign key no existe, continuar
            }
            
            // Intentar eliminar columna
            Schema::table('sellers', function (Blueprint $table) {
                $table->dropColumn('entity_id');
            });
            
            \Log::info('Columna entity_id eliminada de la tabla sellers');
        } catch (\Exception $e) {
            \Log::info('Columna entity_id ya no existe en la tabla sellers: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Restaurar columna entity_id en sellers
        Schema::table('sellers', function (Blueprint $table) {
            $table->foreignId('entity_id')->nullable()->constrained()->onDelete('cascade');
        });

        // 2. Restaurar datos desde la tabla pivote (tomar la primera entidad de cada vendedor)
        $pivotData = DB::table('entity_seller')
            ->select('seller_id', DB::raw('MIN(entity_id) as entity_id'))
            ->groupBy('seller_id')
            ->get();

        foreach ($pivotData as $data) {
            DB::table('sellers')
                ->where('id', $data->seller_id)
                ->update(['entity_id' => $data->entity_id]);
        }

        // 3. Eliminar tabla pivote
        Schema::dropIfExists('entity_seller');
    }

    /**
     * Migrar datos existentes a la tabla pivote
     */
    private function migrateExistingData(): void
    {
        // Obtener todos los sellers con entity_id
        $sellers = DB::table('sellers')
            ->whereNotNull('entity_id')
            ->select('id', 'entity_id')
            ->get();

        // Insertar en la tabla pivote (solo si no existe ya la relación)
        $migrated = 0;
        foreach ($sellers as $seller) {
            $exists = DB::table('entity_seller')
                ->where('entity_id', $seller->entity_id)
                ->where('seller_id', $seller->id)
                ->exists();
            
            if (!$exists) {
                DB::table('entity_seller')->insert([
                    'entity_id' => $seller->entity_id,
                    'seller_id' => $seller->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $migrated++;
            }
        }

        \Log::info("Migrados {$migrated} vendedores a la tabla pivote entity_seller");
    }
};

