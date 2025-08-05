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
        Schema::create('sellers', function (Blueprint $table) {
            // Eliminar las columnas existentes
            $table->id();
            
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('entity_id')->constrained()->onDelete('cascade');

            $table->string("image")->nullable();
            $table->string("name")->nullable();
            $table->string("last_name")->nullable();
            $table->string("last_name2")->nullable();
            $table->string("nif_cif")->nullable();
            $table->string("birthday")->nullable();
            $table->string("email")->nullable();
            $table->string("phone")->nullable();
            $table->string("comment")->nullable();
            $table->integer("status")->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'entity_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            // Revertir los cambios
            $table->dropForeign(['entity_id']);
            $table->dropColumn([
                'user_id', 'image', 'name', 'last_name', 'last_name2', 
                'nif_cif', 'birthday', 'email', 'phone', 'comment', 'status', 'entity_id'
            ]);
        });
    }
};
