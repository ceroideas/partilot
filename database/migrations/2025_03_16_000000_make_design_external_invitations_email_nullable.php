<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * El email se rellena en el paso 2 (enviar invitación), no en el paso 1 (comentario y archivos).
     */
    public function up(): void
    {
        Schema::table('design_external_invitations', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('design_external_invitations', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
        });
    }
};
