<?php

namespace Tests\Feature\Company;

use App\Models\Employee;

class DashboardControllerTest extends CompanyControllerTestCase
{
    use ControllerTestHelpers;

    public function test_guest_cannot_access_dashboard(): void
    {
        $this->get(route('company.dashboard'))
            ->assertRedirectToRoute('login');
    }

    public function test_owner_can_view_dashboard(): void
    {
        $this->actingAsOwner()
            ->get(route('company.dashboard'))
            ->assertOk()
            ->assertViewIs('dashboards.company.index')
            ->assertSeeText($this->owner->name);
    }

    public function test_employee_cannot_access_company_dashboard(): void
    {
        $employeeUser = $this->createEmployeeUser($this->owner, $this->employee);

        $this->actingAs($employeeUser)
            ->get(route('company.dashboard'))
            ->assertForbidden();
    }

    public function test_dashboard_shows_correct_statistics(): void
    {
        Employee::factory()->monthly()->count(3)->create([
            'owner_id' => $this->owner->id,
        ]);
        $this->createDebt($this->employee, ['amount' => 150_000]);

        $this->actingAsOwner()
            ->get(route('company.dashboard'))
            ->assertOk()
            ->assertViewHasAll(['totalEmployees', 'totalDebts', 'totalBalance'])
            ->assertSeeText('5'); // 4 employees (1 base + 3 created) + 1 inactive
    }
}
