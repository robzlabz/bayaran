<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'owner_id' => User::factory()->owner(),
            'name' => fake()->name(),
            'phone' => fake()->unique()->numerify('08##########'),
            'payment_type' => 'monthly',
            'salary_amount' => 4_000_000,
            'daily_rate' => null,
            'hourly_rate' => null,
            'delivery_rate' => null,
            'balance' => 0,
            'leave_quota' => 12,
            'is_active' => true,
            'photo' => null,
        ];
    }

    public function monthly(): static
    {
        return $this->state(fn(array $a) => [
            'payment_type' => 'monthly',
            'salary_amount' => 4_000_000,
            'daily_rate' => null,
            'hourly_rate' => null,
            'delivery_rate' => null,
            'leave_quota' => 12,
        ]);
    }

    public function daily(): static
    {
        return $this->state(fn(array $a) => [
            'payment_type' => 'daily',
            'salary_amount' => null,
            'daily_rate' => 150_000,
            'hourly_rate' => null,
            'delivery_rate' => null,
            'leave_quota' => 0,
        ]);
    }

    public function hourly(): static
    {
        return $this->state(fn(array $a) => [
            'payment_type' => 'hourly',
            'salary_amount' => null,
            'daily_rate' => null,
            'hourly_rate' => 25_000,
            'delivery_rate' => null,
            'leave_quota' => 0,
        ]);
    }

    public function perDelivery(): static
    {
        return $this->state(fn(array $a) => [
            'payment_type' => 'per_delivery',
            'salary_amount' => null,
            'daily_rate' => null,
            'hourly_rate' => null,
            'delivery_rate' => 10_000,
            'leave_quota' => 0,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn(array $a) => [
            'is_active' => false,
        ]);
    }
}
