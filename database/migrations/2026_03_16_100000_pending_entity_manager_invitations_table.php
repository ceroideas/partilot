<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_entity_manager_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->foreignId('entity_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->boolean('permission_sellers')->default(true);
            $table->boolean('permission_design')->default(true);
            $table->boolean('permission_statistics')->default(true);
            $table->boolean('permission_payments')->default(true);
            $table->timestamps();

            $table->unique(['entity_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_entity_manager_invitations');
    }
};
