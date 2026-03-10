<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tarea 9: Diseño e impresión externo. Invitación por email con token; archivos y comentarios.
     */
    public function up(): void
    {
        Schema::create('design_external_invitations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entity_id');
            $table->unsignedBigInteger('lottery_id');
            $table->unsignedBigInteger('set_id');
            $table->unsignedBigInteger('created_by_user_id');
            $table->text('comment')->nullable();
            $table->string('email');
            $table->string('token', 64)->unique();
            $table->string('status', 20)->default('pending'); // pending, sent, in_progress, completed
            $table->timestamp('sent_at')->nullable();
            $table->unsignedBigInteger('design_format_id')->nullable(); // cuando el invitado guarda el diseño
            $table->string('orden_id', 32)->nullable(); // ej. #EN9802 para listado
            $table->timestamps();
        });

        Schema::create('design_external_invitation_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('design_external_invitation_id');
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->timestamps();

            $table->foreign('design_external_invitation_id', 'deif_invitation_fk')
                ->references('id')->on('design_external_invitations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_external_invitation_files');
        Schema::dropIfExists('design_external_invitations');
    }
};
