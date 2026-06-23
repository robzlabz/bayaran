<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add hourly_rate column
        Schema::table('employees', function (Blueprint $table) {
            $table->decimal('hourly_rate', 15, 2)->nullable()->after('delivery_rate')->comment('Upah per jam');
        });

        // Alter check constraint to include 'hourly'
        DB::statement("ALTER TABLE employees DROP CONSTRAINT employees_payment_type_check");
        DB::statement("ALTER TABLE employees ADD CONSTRAINT employees_payment_type_check CHECK (payment_type::text = ANY (ARRAY['monthly'::character varying, 'daily'::character varying, 'per_delivery'::character varying, 'hourly'::character varying]::text[]))");
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('hourly_rate');
        });

        DB::statement("ALTER TABLE employees DROP CONSTRAINT employees_payment_type_check");
        DB::statement("ALTER TABLE employees ADD CONSTRAINT employees_payment_type_check CHECK (payment_type::text = ANY (ARRAY['monthly'::character varying, 'daily'::character varying, 'per_delivery'::character varying]::text[]))");
    }
};
