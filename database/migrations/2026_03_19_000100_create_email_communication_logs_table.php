<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_communication_logs', function (Blueprint $table) {
            $table->id();

            // Referencia opcional a plantilla importada desde el JSON
            $table->string('template_key')->nullable();

            // Tipo interno del mensaje/actuación (mailable/tipo)
            $table->string('message_type')->nullable();

            // Quién envía (requerido por tu especificación)
            $table->string('sender_type'); // superadmin | administracion | entidad
            $table->unsignedBigInteger('sender_user_id')->nullable();

            // A quién se envía
            $table->string('recipient_email');
            $table->string('recipient_role')->nullable();
            $table->unsignedBigInteger('recipient_user_id')->nullable();

            // Para reenviar: guardamos clase y payload (id simple) para poder reconstruir el Mailable
            $table->string('mail_class')->nullable();
            $table->json('mail_payload')->nullable();

            // Estados
            $table->string('status', 20); // pending | sent | cancelled | resent
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('resent_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('last_attempt_at')->nullable();

            $table->text('error_message')->nullable();
            $table->json('context')->nullable();

            $table->timestamps();

            $table->index(['recipient_email', 'status']);
            $table->index(['sender_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_communication_logs');
    }
};

