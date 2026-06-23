<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->enum('payment_type', ['monthly', 'daily', 'per_delivery']);
            $table->decimal('salary_amount', 15, 2)->nullable()->comment('Gaji bulanan');
            $table->decimal('daily_rate', 15, 2)->nullable()->comment('Upah harian');
            $table->decimal('delivery_rate', 15, 2)->nullable()->comment('Tarif per pengantaran');
            $table->decimal('balance', 15, 2)->default(0)->comment('Saldo untuk daily/per_delivery');
            $table->boolean('is_active')->default(true);
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
