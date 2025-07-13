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
        Schema::create('administrations', function (Blueprint $table) {
            $table->id();
            $table->string("web")->nullable();
            $table->string("image")->nullable();
            $table->string("name")->nullable();
            $table->string("receiving")->nullable();
            $table->string("society")->nullable();
            $table->string("nif_cif")->nullable();
            $table->string("province")->nullable();
            $table->string("city")->nullable();
            $table->string("postal_code")->nullable();
            $table->string("address")->nullable();
            $table->string("email")->nullable();
            $table->string("phone")->nullable();
            $table->string("account")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administrations');
    }
};
