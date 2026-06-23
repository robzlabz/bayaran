<?php

namespace Tests\Feature\Company;

use App\Models\Employee;
use App\Models\Leave;
use App\Models\User;

class LeaveControllerTest extends CompanyControllerTestCase
{
    use ControllerTestHelpers;

    // ─── ACCESS CONTROL ───────────────────────────────────

    public function test_guest_cannot_access_leaves(): void
    {
        $this->get(route('company.leaves.index'))
            ->assertRedirectToRoute('login');
    }

    public function test_employee_role_cannot_access_leaves(): void
    {
        $this->actingAs($this->createEmployeeUser($this->owner, $this->employee))
            ->get(route('company.leaves.index'))
            ->assertForbidden();
    }

    // ─── INDEX ────────────────────────────────────────────

    public function test_index_lists_leaves(): void
    {
        $this->createLeave($this->employee, ['type' => 'sick', 'reason' => 'Demam']);

        $this->actingAsOwner()
            ->get(route('company.leaves.index'))
            ->assertOk()
            ->assertViewIs('leaves.index')
            ->assertViewHasAll(['leaves', 'employees', 'year', 'totalLeaves'])
            ->assertSeeText('Demam');
    }

    public function test_index_filters_by_employee(): void
    {
        $employee2 = Employee::factory()->daily()->create(['owner_id' => $this->owner->id]);
        $this->createLeave($this->employee, ['type' => 'sick']);
        $this->createLeave($employee2, ['type' => 'permission']);

        $response = $this->actingAsOwner()
            ->get(route('company.leaves.index', ['employee_id' => $this->employee->id]))
            ->assertOk();

        $this->assertCount(1, $response->viewData('leaves'));
    }

    public function test_index_filters_by_year(): void
    {
        $this->createLeave($this->employee, [
            'type' => 'annual_leave',
            'start_date' => '2025-06-01',
            'end_date' => '2025-06-03',
        ]);

        $response = $this->actingAsOwner()
            ->get(route('company.leaves.index', ['year' => 2025]))
            ->assertOk();

        $this->assertEquals(1, $response->viewData('leaves')->total());
    }

    public function test_index_prevents_seeing_other_owners_leaves(): void
    {
        $otherOwner = User::factory()->owner()->create();
        $otherEmployee = Employee::factory()->create(['owner_id' => $otherOwner->id]);
        $this->createLeave($otherEmployee, ['type' => 'sick', 'reason' => 'Rahasia']);

        $this->actingAsOwner()
            ->get(route('company.leaves.index'))
            ->assertOk()
            ->assertDontSeeText('Rahasia');
    }

    // ─── CREATE ───────────────────────────────────────────

    public function test_create_shows_active_employees(): void
    {
        $this->actingAsOwner()
            ->get(route('company.leaves.create'))
            ->assertOk()
            ->assertViewIs('leaves.create')
            ->assertSeeText($this->employee->name)
            ->assertDontSeeText($this->employeeInactive->name);
    }

    // ─── STORE ────────────────────────────────────────────

    public function test_store_creates_leave_as_approved(): void
    {
        $this->actingAsOwner()
            ->post(route('company.leaves.store'), [
                'employee_id' => $this->employee->id,
                'type' => 'annual_leave',
                'start_date' => '2026-07-10',
                'end_date' => '2026-07-12',
                'reason' => 'Liburan keluarga',
            ])
            ->assertRedirectToRoute('company.leaves.index');

        $this->assertDatabaseHas('leaves', [
            'employee_id' => $this->employee->id,
            'type' => 'annual_leave',
            'status' => 'approved',
            'approved_by' => $this->owner->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAsOwner()
            ->post(route('company.leaves.store'), [])
            ->assertSessionHasErrors(['employee_id', 'type', 'start_date', 'end_date']);
    }

    public function test_store_validates_end_date_after_start(): void
    {
        $this->actingAsOwner()
            ->post(route('company.leaves.store'), [
                'employee_id' => $this->employee->id,
                'type' => 'sick',
                'start_date' => '2026-07-15',
                'end_date' => '2026-07-10',
            ])
            ->assertSessionHasErrors(['end_date']);
    }

    public function test_store_prevents_other_owners_employee(): void
    {
        $otherOwner = User::factory()->owner()->create();
        $otherEmployee = Employee::factory()->create(['owner_id' => $otherOwner->id]);

        $this->actingAsOwner()
            ->post(route('company.leaves.store'), [
                'employee_id' => $otherEmployee->id,
                'type' => 'sick',
                'start_date' => '2026-07-01',
                'end_date' => '2026-07-01',
            ])
            ->assertForbidden();
    }

    // ─── DESTROY ──────────────────────────────────────────

    public function test_destroy_deletes_leave(): void
    {
        $leave = $this->createLeave($this->employee);

        $this->actingAsOwner()
            ->delete(route('company.leaves.destroy', $leave))
            ->assertRedirectToRoute('company.leaves.index');

        $this->assertDatabaseMissing('leaves', ['id' => $leave->id]);
    }

    public function test_destroy_prevents_other_owners_leave(): void
    {
        $otherOwner = User::factory()->owner()->create();
        $otherEmployee = Employee::factory()->create(['owner_id' => $otherOwner->id]);

        $leave = new \App\Models\Leave([
            'employee_id' => $otherEmployee->id,
            'type' => 'annual_leave',
            'start_date' => today(),
            'end_date' => today(),
            'reason' => 'Other owner',
            'status' => 'approved',
            'approved_by' => $otherOwner->id,
        ]);
        $leave->save();

        $this->actingAsOwner()
            ->delete(route('company.leaves.destroy', $leave))
            ->assertForbidden();
    }
}
