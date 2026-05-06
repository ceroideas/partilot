<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('nif_cif', 50)->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('province', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->decimal('price_design', 10, 4)->default(0);
            $table->decimal('price_participation', 10, 4)->default(0);
            $table->decimal('price_back_bw', 10, 4)->default(0);
            $table->decimal('price_back_color', 10, 4)->default(0);
            $table->decimal('price_taco_25', 10, 4)->default(0);
            $table->decimal('price_taco_50', 10, 4)->default(0);
            $table->decimal('price_taco_100', 10, 4)->default(0);
            $table->string('bank_account', 80)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_configurations');
    }
};

