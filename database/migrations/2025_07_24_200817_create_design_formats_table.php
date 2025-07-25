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
        Schema::create('design_formats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entity_id');
            $table->unsignedBigInteger('lottery_id');
            $table->unsignedBigInteger('set_id');
            $table->string('format')->nullable();
            $table->string('page')->nullable();
            $table->integer('rows')->nullable();
            $table->integer('cols')->nullable();
            $table->string('orientation')->nullable();
            $table->decimal('margin_up', 8, 2)->nullable();
            $table->decimal('margin_right', 8, 2)->nullable();
            $table->decimal('margin_left', 8, 2)->nullable();
            $table->decimal('margin_top', 8, 2)->nullable();
            $table->decimal('identation', 8, 2)->nullable();
            $table->decimal('matrix_box', 8, 2)->nullable();
            $table->decimal('page_rigth', 8, 2)->nullable();
            $table->decimal('page_bottom', 8, 2)->nullable();
            $table->string('guide_color', 20)->nullable();
            $table->decimal('guide_weight', 8, 2)->nullable();
            $table->integer('participation_number')->nullable();
            $table->integer('participation_from')->nullable();
            $table->integer('participation_to')->nullable();
            $table->integer('participation_page')->nullable();
            $table->boolean('guides')->nullable();
            $table->string('generate', 10)->nullable();
            $table->string('documents', 10)->nullable();
            $table->json('blocks')->nullable(); // HTML de los containment-wrapper
            $table->longText('participation_html')->nullable();
            $table->longText('cover_html')->nullable();
            $table->longText('back_html')->nullable();
            $table->json('backgrounds')->nullable();
            $table->json('output')->nullable();
            $table->timestamps();

            // Foreign keys (opcional, si existen las tablas referenciadas)
            // $table->foreign('entity_id')->references('id')->on('entities');
            // $table->foreign('lottery_id')->references('id')->on('lotteries');
            // $table->foreign('set_id')->references('id')->on('sets');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_formats');
    }
};
