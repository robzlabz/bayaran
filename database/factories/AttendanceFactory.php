<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        $clockIn = fake()->dateTimeBetween('-1 month', 'now');

        return [
            'employee_id' => Employee::factory(),
            'clock_in' => $clockIn,
            'clock_out' => (clone $clockIn)->modify('+'.fake()->numberBetween(4, 9).' hours'),
            'clock_in_photo' => null,
            'clock_out_photo' => null,
            'is_manual_entry' => false,
            'is_clock_in_manual' => false,
            'is_clock_out_manual' => false,
            'notes' => null,
            'work_hours' => null,
        ];
    }

    public function manual(): static
    {
        return $this->state(fn(array $a) => [
            'is_manual_entry' => true,
            'is_clock_in_manual' => true,
            'clock_in_photo' => null,
            'clock_out_photo' => null,
        ]);
    }

    public function clockedIn(): static
    {
        return $this->state(fn(array $a) => [
            'clock_in' => now()->subHours(fake()->numberBetween(1, 8)),
            'clock_out' => null,
        ]);
    }

    public function forDate(string $date): static
    {
        $clockIn = $date.' '.fake()->numberBetween(7, 9).':'.fake()->numberBetween(0, 59).':00';

        return $this->state(fn(array $a) => [
            'clock_in' => $clockIn,
            'clock_out' => $date.' '.fake()->numberBetween(16, 18).':'.fake()->numberBetween(0, 59).':00',
        ]);
    }
}
