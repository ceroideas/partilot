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
        Schema::create('managers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('entity_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('administration_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Asegurar que un usuario solo puede ser manager de una entidad o administración
            $table->unique(['user_id', 'entity_id']);
            $table->unique(['user_id', 'administration_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('managers');
    }
};
