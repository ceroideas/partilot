<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();

            // Clave estable derivada del título del JSON (para poder referenciar la plantilla)
            $table->string('key')->unique();
            $table->string('title')->nullable();

            // Texto “crudo” con cuándo dispara / condiciones (para depurar y usar en fases futuras)
            $table->text('trigger_text')->nullable();
            $table->text('condition_text')->nullable();

            // Plantilla: asunto y cuerpo (para render futuro desde backend)
            $table->string('subject_template')->nullable();
            $table->longText('body_template')->nullable();

            // Flags importados del JSON (no se usan aún para automatizar disparos)
            $table->boolean('enabled_email')->default(true);
            $table->boolean('enabled_notification')->default(false);

            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};

