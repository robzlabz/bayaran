<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pivot: which debts were paid in which payment
        Schema::create('debt_payment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2)->comment('Jumlah yg dibayar untuk hutang ini');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debt_payment');
    }
};
