<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'type' => 'topup',
            'amount' => 100_000,
            'balance_before' => 0,
            'balance_after' => 100_000,
            'description' => 'Top up saldo',
            'transaction_date' => today(),
        ];
    }

    public function topup(): static
    {
        return $this->state(fn(array $a) => [
            'type' => 'topup',
            'amount' => fn(array $s) => abs($s['amount'] ?? 100_000),
        ]);
    }

    public function transport(): static
    {
        return $this->state(fn(array $a) => [
            'type' => 'transport',
            'amount' => fn(array $s) => -abs($s['amount'] ?? 50_000),
        ]);
    }

    public function salary(): static
    {
        return $this->state(fn(array $a) => [
            'type' => 'salary',
            'amount' => fn(array $s) => -abs($s['amount'] ?? 500_000),
        ]);
    }
}
