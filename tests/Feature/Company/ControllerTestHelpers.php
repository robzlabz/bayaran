<?php

namespace Tests\Feature\Company;

use App\Models\Employee;
use App\Models\User;

trait ControllerTestHelpers
{
    /**
     * Create a User record (role=employee) linked to an owner and employee record.
     */
    protected function createEmployeeUser(User $owner, Employee $employee): User
    {
        return User::factory()->create([
            'name' => $employee->name,
            'phone' => $employee->phone,
            'email' => $employee->phone . '@bayaran.app',
            'password' => bcrypt('password'),
            'role' => 'employee',
            'owner_id' => $owner->id,
            'employee_id' => $employee->id,
            'account_type' => 'personal',
        ]);
    }
}
