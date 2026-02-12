<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participation_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nombre');
            $table->string('apellidos');
            $table->string('nif', 20);
            $table->string('iban', 24);
            $table->decimal('importe_total', 10, 2);
            $table->timestamp('collected_at');
            $table->timestamps();
            
            $table->index(['user_id', 'collected_at']);
        });
        
        Schema::create('participation_collection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained('participation_collections')->onDelete('cascade');
            $table->foreignId('participation_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['collection_id', 'participation_id'], 'part_col_items_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participation_collection_items');
        Schema::dropIfExists('participation_collections');
    }
};
