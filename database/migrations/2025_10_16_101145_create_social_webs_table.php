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
        Schema::create('social_webs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('banner_image')->nullable();
            $table->string('small_image')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_webs');
    }
};
