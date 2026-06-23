<?php

namespace Tests\Feature\Company;

use App\Models\Attendance;
use App\Models\Debt;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Transaction;
use App\Models\User;
use Tests\TestCase;

abstract class CompanyControllerTestCase extends TestCase
{
    use DatabaseSetup;

    protected User $owner;
    protected Employee $employee;
    protected Employee $employeeInactive;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase();

        $this->owner = User::factory()->owner()->create();
        $this->employee = Employee::factory()->monthly()->create([
            'owner_id' => $this->owner->id,
        ]);
        $this->employeeInactive = Employee::factory()->monthly()->inactive()->create([
            'owner_id' => $this->owner->id,
        ]);
    }

    protected function tearDown(): void
    {
        $this->tearDownDatabase();
        parent::tearDown();
    }

    /**
     * Act as the owner user.
     */
    protected function actingAsOwner(): static
    {
        return $this->actingAs($this->owner);
    }

    /**
     * Create a debt for the given employee.
     */
    protected function createDebt(Employee $employee, array $overrides = []): Debt
    {
        return Debt::factory()->unpaid()->create(array_merge([
            'employee_id' => $employee->id,
        ], $overrides));
    }

    /**
     * Create a topup transaction for the given employee.
     */
    protected function createTopup(Employee $employee, int $amount = 100_000): Transaction
    {
        $balanceBefore = $employee->fresh()->balance;

        $tx = Transaction::factory()->topup()->create([
            'employee_id' => $employee->id,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceBefore + $amount,
            'transaction_date' => today(),
        ]);

        $employee->increment('balance', $amount);

        return $tx;
    }

    /**
     * Create an attendance record for the given employee.
     */
    protected function createAttendance(Employee $employee, string $date): Attendance
    {
        return Attendance::factory()->forDate($date)->create([
            'employee_id' => $employee->id,
        ]);
    }

    /**
     * Create a leave record for the given employee.
     */
    protected function createLeave(Employee $employee, array $overrides = []): Leave
    {
        return Leave::factory()->create(array_merge([
            'employee_id' => $employee->id,
            'approved_by' => $this->owner->id,
        ], $overrides));
    }
}
