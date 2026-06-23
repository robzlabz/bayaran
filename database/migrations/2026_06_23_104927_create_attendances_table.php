<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->dateTime('clock_in');
            $table->dateTime('clock_out')->nullable();
            $table->string('clock_in_photo')->nullable();
            $table->string('clock_out_photo')->nullable();
            $table->boolean('is_manual_entry')->default(false);
            $table->boolean('is_clock_in_manual')->default(false);
            $table->boolean('is_clock_out_manual')->default(false);
            $table->text('notes')->nullable();
            $table->decimal('work_hours', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
