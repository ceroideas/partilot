<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participation_donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nombre')->nullable();
            $table->string('apellidos')->nullable();
            $table->string('nif', 20)->nullable();
            $table->decimal('importe_donacion', 10, 2)->default(0);
            $table->decimal('importe_codigo', 10, 2)->default(0);
            $table->string('codigo_recarga', 20)->nullable();
            $table->boolean('anonima')->default(false);
            $table->timestamp('donated_at');
            $table->timestamps();
            
            $table->index(['user_id', 'donated_at']);
            $table->index('codigo_recarga');
        });
        
        Schema::create('participation_donation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained('participation_donations')->onDelete('cascade');
            $table->foreignId('participation_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['donation_id', 'participation_id'], 'part_don_items_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participation_donation_items');
        Schema::dropIfExists('participation_donations');
    }
};
