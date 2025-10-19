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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('entity_id')->nullable()->constrained('entities')->onDelete('cascade');
            $table->foreignId('administration_id')->nullable()->constrained('administrations')->onDelete('cascade');
            $table->enum('status', ['sent', 'delivered', 'read', 'failed'])->default('sent');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Índices para optimizar consultas
            $table->index(['entity_id', 'status']);
            $table->index(['administration_id', 'status']);
            $table->index(['sender_id', 'created_at']);
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
