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
        Schema::create('entities', function (Blueprint $table) {
            $table->id();
            $table->integer('administration_id')->nullable();
            $table->integer('manager_id')->nullable();
            $table->string("image")->nullable();
            $table->string("name")->nullable();
            $table->string("province")->nullable();
            $table->string("city")->nullable();
            $table->string("postal_code")->nullable();
            $table->string("address")->nullable();
            $table->string("nif_cif")->nullable();
            $table->string("phone")->nullable();
            $table->string("email")->nullable();
            $table->string("comments")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};
