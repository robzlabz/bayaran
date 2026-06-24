<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add pay_date to employees
        Schema::table('employees', function (Blueprint $table) {
            $table->tinyInteger('pay_date')->nullable()->after('leave_quota')->comment('Tanggal gajian (1-31)');
        });

        // Create payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_amount', 15, 2)->comment('Total dibayarkan');
            $table->decimal('salary_amount', 15, 2)->default(0)->comment('Porsi gaji');
            $table->decimal('debt_amount', 15, 2)->default(0)->comment('Porsi bayar hutang');
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('pay_date');
        });
    }
};
