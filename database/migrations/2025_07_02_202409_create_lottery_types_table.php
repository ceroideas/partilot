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
        Schema::create('lottery_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del tipo de sorteo
            $table->decimal('ticket_price', 10, 2); // Precio del décimo
            $table->json('prize_categories'); // Categorías de premios en JSON
            $table->boolean('is_active')->default(true); // Si está activo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lottery_types');
    }
};
