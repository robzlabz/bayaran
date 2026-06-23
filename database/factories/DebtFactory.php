<?php

namespace Database\Factories;

use App\Models\Debt;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Debt>
 */
class DebtFactory extends Factory
{
    protected $model = Debt::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'amount' => fake()->numberBetween(10_000, 500_000),
            'description' => fake()->sentence(),
            'debt_date' => fake()->date(),
            'is_paid' => false,
            'paid_at' => null,
            'notes' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn(array $a) => [
            'is_paid' => true,
            'paid_at' => now(),
        ]);
    }

    public function unpaid(): static
    {
        return $this->state(fn(array $a) => [
            'is_paid' => false,
            'paid_at' => null,
        ]);
    }
}
