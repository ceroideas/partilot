<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_digital_sales', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->foreignId('entity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lottery_id')->constrained()->cascadeOnDelete();
            $table->foreignId('set_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('sale_amount', 12, 2)->default(0);
            $table->string('payment_method', 30)->nullable();
            $table->string('registration_token', 64)->unique();
            $table->string('status', 20)->default('pending')->index();
            $table->timestamp('valid_until')->index();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('pending_digital_sale_participations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pending_digital_sale_id');
            $table->unsignedBigInteger('participation_id');
            $table->foreign('pending_digital_sale_id', 'pds_pdsp_sale_fk')
                ->references('id')->on('pending_digital_sales')->cascadeOnDelete();
            $table->foreign('participation_id', 'pds_pdsp_part_fk')
                ->references('id')->on('participations')->cascadeOnDelete();
            $table->unique(['participation_id']);
        });

        Schema::create('phone_verification_codes', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 20)->index();
            $table->string('code_hash', 64);
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phone_verification_codes');
        Schema::dropIfExists('pending_digital_sale_participations');
        Schema::dropIfExists('pending_digital_sales');
    }
};
