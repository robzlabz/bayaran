<?php

namespace Tests\Feature\Company;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Trait to set up database schema without RefreshDatabase.
 *
 * Why not RefreshDatabase?
 * - The migration `add_hourly_rate_to_employees_table` uses PostgreSQL-specific
 *   `ALTER TABLE ... DROP CONSTRAINT` which fails on SQLite.
 *
 * This trait:
 * 1. Creates the schema via raw SQLite-compatible DDL (once per class)
 * 2. Truncates all tables after each test for isolation (no transactions needed)
 *
 * Why truncation instead of transactions?
 * - SQLite :memory: can have connection-sharing issues with transactions
 * - Truncation is simpler and more reliable across HTTP request boundaries
 *
 * Usage in your test class:
 *   use DatabaseSetup;
 */
trait DatabaseSetup
{
    protected function setUpDatabase(): void
    {
        $this->createSchema();
    }

    protected function tearDownDatabase(): void
    {
        // Truncate all tables to clean up test data
        $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
        DB::statement('PRAGMA foreign_keys = OFF');
        foreach ($tables as $table) {
            DB::statement("DELETE FROM \"{$table->name}\"");
        }
        DB::statement('PRAGMA foreign_keys = ON');
    }

    /**
     * Create all tables with raw DDL that works on SQLite.
     * Foreign keys are defined inline (SQLite cannot ALTER TABLE ADD CONSTRAINT).
     */
    private function createSchema(): void
    {
        DB::statement('PRAGMA foreign_keys = ON');

        // Drop all existing tables first (safe for repeated calls across tests)
        DB::statement('PRAGMA foreign_keys = OFF');
        $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
        foreach ($tables as $table) {
            Schema::dropIfExists($table->name);
        }
        DB::statement('PRAGMA foreign_keys = ON');

        Schema::create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('account_type')->default('personal');
            $table->string('company_name')->nullable();
            $table->string('phone', 20)->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('password_default')->nullable();
            $table->string('role')->default('owner');
            $table->unsignedBigInteger('owner_id')->nullable()->index();
            $table->unsignedBigInteger('employee_id')->nullable()->index();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function ($table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function ($table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('employees', function ($table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->string('payment_type');
            $table->decimal('salary_amount', 15, 2)->nullable();
            $table->decimal('daily_rate', 15, 2)->nullable();
            $table->decimal('delivery_rate', 15, 2)->nullable();
            $table->decimal('hourly_rate', 15, 2)->nullable();
            $table->integer('leave_quota')->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('photo')->nullable();
            $table->timestamps();
        });

        Schema::create('debts', function ($table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->date('debt_date');
            $table->boolean('is_paid')->default(false);
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('transactions', function ($table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2)->default(0);
            $table->decimal('balance_after', 15, 2)->default(0);
            $table->string('description')->nullable();
            $table->date('transaction_date');
            $table->timestamps();
        });

        Schema::create('attendances', function ($table) {
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

        Schema::create('leaves', function ($table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('permission');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason')->nullable();
            $table->string('status')->default('approved');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('cache', function ($table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function ($table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('jobs', function ($table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function ($table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });
    }
}
