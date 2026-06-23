<?php

namespace Database\Factories;

use App\Models\Leave;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Leave>
 */
class LeaveFactory extends Factory
{
    protected $model = Leave::class;

    /**
     * Define the model's default state.
     *
     * NOTE: employee_id and approved_by are not set here intentionally.
     * Callers MUST provide them explicitly to avoid phantom Employee/User
     * creation that can cause route model binding lookup failures.
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', '+1 month');

        return [
            'type' => fake()->randomElement(['sick', 'permission', 'annual_leave']),
            'start_date' => $startDate,
            'end_date' => (clone $startDate)->modify('+'.fake()->numberBetween(0, 2).' days'),
            'reason' => fake()->sentence(),
            'status' => 'approved',
        ];
    }

    public function sick(): static
    {
        return $this->state(fn(array $a) => ['type' => 'sick']);
    }

    public function permission(): static
    {
        return $this->state(fn(array $a) => ['type' => 'permission']);
    }

    public function annualLeave(): static
    {
        return $this->state(fn(array $a) => ['type' => 'annual_leave']);
    }

    public function pending(): static
    {
        return $this->state(fn(array $a) => ['status' => 'pending']);
    }

    public function rejected(): static
    {
        return $this->state(fn(array $a) => ['status' => 'rejected']);
    }
}
