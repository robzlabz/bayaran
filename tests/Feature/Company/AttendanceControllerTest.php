<?php

namespace Tests\Feature\Company;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;

class AttendanceControllerTest extends CompanyControllerTestCase
{
    use ControllerTestHelpers;

    // ─── ACCESS CONTROL ───────────────────────────────────

    public function test_guest_cannot_access_attendances(): void
    {
        $this->get(route('company.attendances.index'))
            ->assertRedirectToRoute('login');
    }

    public function test_employee_role_cannot_access_attendances(): void
    {
        $this->actingAs($this->createEmployeeUser($this->owner, $this->employee))
            ->get(route('company.attendances.index'))
            ->assertForbidden();
    }

    // ─── INDEX ────────────────────────────────────────────

    public function test_index_lists_todays_attendances(): void
    {
        $this->createAttendance($this->employee, today()->format('Y-m-d'));

        $this->actingAsOwner()
            ->get(route('company.attendances.index'))
            ->assertOk()
            ->assertViewIs('attendances.index')
            ->assertViewHasAll(['attendances', 'employees', 'date', 'todayCount', 'activeEmployees']);
    }

    public function test_index_filters_by_date(): void
    {
        $this->createAttendance($this->employee, '2026-01-15');
        $this->createAttendance($this->employee, '2026-01-16');

        $response = $this->actingAsOwner()
            ->get(route('company.attendances.index', ['date' => '2026-01-15']))
            ->assertOk();

        $this->assertCount(1, $response->viewData('attendances'));
    }

    public function test_index_filters_by_employee(): void
    {
        $employee2 = Employee::factory()->daily()->create(['owner_id' => $this->owner->id]);
        $this->createAttendance($this->employee, today()->format('Y-m-d'));
        $this->createAttendance($employee2, today()->format('Y-m-d'));

        $response = $this->actingAsOwner()
            ->get(route('company.attendances.index', ['employee_id' => $this->employee->id]))
            ->assertOk();

        $this->assertCount(1, $response->viewData('attendances'));
    }

    public function test_index_prevents_seeing_other_owners_attendances(): void
    {
        $otherOwner = User::factory()->owner()->create();
        $otherEmployee = Employee::factory()->create(['owner_id' => $otherOwner->id]);
        $this->createAttendance($otherEmployee, today()->format('Y-m-d'));

        $this->actingAsOwner()
            ->get(route('company.attendances.index'))
            ->assertOk();

        $this->assertEmpty(Attendance::forOwner($this->owner->id)->get());
    }

    // ─── CREATE ───────────────────────────────────────────

    public function test_create_shows_active_employees(): void
    {
        $this->actingAsOwner()
            ->get(route('company.attendances.create'))
            ->assertOk()
            ->assertViewIs('attendances.create')
            ->assertSeeText($this->employee->name)
            ->assertDontSeeText($this->employeeInactive->name);
    }

    // ─── STORE (manual entry) ─────────────────────────────

    public function test_store_creates_manual_attendance(): void
    {
        $clockIn = today()->subHours(8)->format('Y-m-d H:i:s');
        $clockOut = today()->subHours(1)->format('Y-m-d H:i:s');

        $this->actingAsOwner()
            ->post(route('company.attendances.store'), [
                'employee_id' => $this->employee->id,
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
                'notes' => 'Lupa absen',
            ])
            ->assertRedirectToRoute('company.attendances.index')
            ;

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->employee->id,
            'is_manual_entry' => true,
            'notes' => 'Lupa absen',
        ]);
    }

    public function test_store_clock_in_only(): void
    {
        $clockIn = today()->subHours(5)->format('Y-m-d H:i:s');

        $this->actingAsOwner()
            ->post(route('company.attendances.store'), [
                'employee_id' => $this->employee->id,
                'clock_in' => $clockIn,
                'clock_out' => '',
                'notes' => 'Clock in aja',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->employee->id,
            'clock_out' => null,
            'is_clock_in_manual' => true,
        ]);
    }

    public function test_store_validates_clock_out_after_clock_in(): void
    {
        $this->actingAsOwner()
            ->post(route('company.attendances.store'), [
                'employee_id' => $this->employee->id,
                'clock_in' => '2026-06-23 17:00:00',
                'clock_out' => '2026-06-23 08:00:00',
            ])
            ->assertSessionHasErrors(['clock_out']);
    }

    public function test_store_prevents_other_owners_employee(): void
    {
        $otherOwner = User::factory()->owner()->create();
        $otherEmployee = Employee::factory()->create(['owner_id' => $otherOwner->id]);

        $this->actingAsOwner()
            ->post(route('company.attendances.store'), [
                'employee_id' => $otherEmployee->id,
                'clock_in' => now()->format('Y-m-d H:i:s'),
            ])
            ->assertForbidden();
    }

    // ─── EDIT ─────────────────────────────────────────────

    public function test_edit_shows_attendance(): void
    {
        $attendance = $this->createAttendance($this->employee, today()->format('Y-m-d'));

        $this->actingAsOwner()
            ->get(route('company.attendances.edit', $attendance))
            ->assertOk()
            ->assertViewIs('attendances.edit')
            ->assertViewHasAll(['attendance', 'employees']);
    }

    public function test_edit_prevents_other_owners_attendance(): void
    {
        $otherOwner = User::factory()->owner()->create();
        $otherEmployee = Employee::factory()->create(['owner_id' => $otherOwner->id]);
        $attendance = $this->createAttendance($otherEmployee, today()->format('Y-m-d'));

        $this->actingAsOwner()
            ->get(route('company.attendances.edit', $attendance))
            ->assertForbidden();
    }

    // ─── UPDATE ───────────────────────────────────────────

    public function test_update_changes_attendance(): void
    {
        $attendance = $this->createAttendance($this->employee, today()->format('Y-m-d'));
        $newClockIn = today()->subHours(9)->format('Y-m-d H:i:s');
        $newClockOut = today()->subHours(2)->format('Y-m-d H:i:s');

        $this->actingAsOwner()
            ->put(route('company.attendances.update', $attendance), [
                'clock_in' => $newClockIn,
                'clock_out' => $newClockOut,
                'notes' => 'Diperbarui',
            ])
            ->assertRedirect()
            ;

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'notes' => 'Diperbarui',
        ]);
    }

    public function test_update_can_remove_clock_out(): void
    {
        $attendance = $this->createAttendance($this->employee, today()->format('Y-m-d'));

        $this->actingAsOwner()
            ->put(route('company.attendances.update', $attendance), [
                'clock_in' => $attendance->clock_in->format('Y-m-d H:i:s'),
                'clock_out' => '',
                'notes' => 'Hapus clock out',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'clock_out' => null,
            'work_hours' => null,
        ]);
    }

    public function test_update_prevents_other_owners_attendance(): void
    {
        $otherOwner = User::factory()->owner()->create();
        $otherEmployee = Employee::factory()->create(['owner_id' => $otherOwner->id]);
        $attendance = $this->createAttendance($otherEmployee, today()->format('Y-m-d'));

        $this->actingAsOwner()
            ->put(route('company.attendances.update', $attendance), [
                'clock_in' => now()->format('Y-m-d H:i:s'),
            ])
            ->assertForbidden();
    }

    // ─── DESTROY ──────────────────────────────────────────

    public function test_destroy_deletes_attendance(): void
    {
        $attendance = $this->createAttendance($this->employee, today()->format('Y-m-d'));

        $this->actingAsOwner()
            ->delete(route('company.attendances.destroy', $attendance))
            ->assertRedirectToRoute('company.attendances.index')
            ;

        $this->assertDatabaseMissing('attendances', ['id' => $attendance->id]);
    }

    public function test_destroy_prevents_other_owners_attendance(): void
    {
        $otherOwner = User::factory()->owner()->create();
        $otherEmployee = Employee::factory()->create(['owner_id' => $otherOwner->id]);
        $attendance = $this->createAttendance($otherEmployee, today()->format('Y-m-d'));

        $this->actingAsOwner()
            ->delete(route('company.attendances.destroy', $attendance))
            ->assertForbidden();
    }
}
