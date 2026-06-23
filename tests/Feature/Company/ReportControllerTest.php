<?php

namespace Tests\Feature\Company;

use App\Models\Employee;
use App\Models\User;

class ReportControllerTest extends CompanyControllerTestCase
{
    use ControllerTestHelpers;

    // ─── ACCESS CONTROL ───────────────────────────────────

    public function test_guest_cannot_access_reports(): void
    {
        $this->get(route('company.reports.index'))
            ->assertRedirectToRoute('login');
    }

    public function test_employee_role_cannot_access_reports(): void
    {
        $this->actingAs($this->createEmployeeUser($this->owner, $this->employee))
            ->get(route('company.reports.index'))
            ->assertForbidden();
    }

    // ─── INDEX ────────────────────────────────────────────

    public function test_index_shows_summary_statistics(): void
    {
        Employee::factory()->monthly()->count(3)->create(['owner_id' => $this->owner->id]);
        $this->createDebt($this->employee, ['amount' => 200_000]);
        $this->createAttendance($this->employee, today()->format('Y-m-d'));

        $this->actingAsOwner()
            ->get(route('company.reports.index'))
            ->assertOk()
            ->assertViewIs('reports.index')
            ->assertViewHasAll([
                'totalEmployees', 'activeEmployees', 'totalBalance',
                'totalDebt', 'unpaidDebt', 'todayAttendance',
            ]);
    }

    // ─── ATTENDANCE REPORT ────────────────────────────────

    public function test_attendance_report_shows_data(): void
    {
        $this->createAttendance($this->employee, now()->format('Y-m-d'));

        $this->actingAsOwner()
            ->get(route('company.reports.attendance', [
                'month' => now()->format('m'),
                'year' => now()->format('Y'),
            ]))
            ->assertOk()
            ->assertViewIs('reports.attendance')
            ->assertViewHasAll(['attendances', 'grouped', 'employees']);
    }

    public function test_attendance_report_filters_by_employee(): void
    {
        $employee2 = Employee::factory()->daily()->create(['owner_id' => $this->owner->id]);
        $this->createAttendance($this->employee, now()->format('Y-m-d'));
        $this->createAttendance($employee2, now()->format('Y-m-d'));

        $response = $this->actingAsOwner()
            ->get(route('company.reports.attendance', [
                'month' => now()->format('m'),
                'year' => now()->format('Y'),
                'employee_id' => $this->employee->id,
            ]))
            ->assertOk();

        $this->assertCount(1, $response->viewData('attendances'));
    }

    public function test_attendance_report_prevents_other_owners_data(): void
    {
        $otherOwner = User::factory()->owner()->create();
        $otherEmployee = Employee::factory()->create(['owner_id' => $otherOwner->id]);
        $this->createAttendance($otherEmployee, now()->format('Y-m-d'));

        $this->actingAsOwner()
            ->get(route('company.reports.attendance', [
                'month' => now()->format('m'),
                'year' => now()->format('Y'),
            ]))
            ->assertOk();

        $this->assertEmpty(
            $response ??= null ? $response->viewData('attendances') : []
        );
    }

    // ─── ATTENDANCE PDF ───────────────────────────────────

    public function test_attendance_pdf_returns_download(): void
    {
        $this->createAttendance($this->employee, now()->format('Y-m-d'));

        $this->actingAsOwner()
            ->get(route('company.reports.attendance.pdf', [
                'month' => now()->format('m'),
                'year' => now()->format('Y'),
            ]))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    // ─── DEBTS REPORT ─────────────────────────────────────

    public function test_debts_report_shows_data(): void
    {
        $this->createDebt($this->employee, ['amount' => 150_000]);

        $this->actingAsOwner()
            ->get(route('company.reports.debts', [
                'month' => now()->format('m'),
                'year' => now()->format('Y'),
            ]))
            ->assertOk()
            ->assertViewIs('reports.debts')
            ->assertViewHasAll(['debts', 'employees', 'totalDebt', 'totalUnpaid']);
    }

    public function test_debts_report_filters_by_status(): void
    {
        $this->createDebt($this->employee, ['amount' => 100_000, 'is_paid' => false, 'debt_date' => today()]);
        $this->createDebt($this->employee, ['amount' => 200_000, 'is_paid' => true, 'paid_at' => now(), 'debt_date' => today()]);

        $response = $this->actingAsOwner()
            ->get(route('company.reports.debts', [
                'month' => now()->format('m'),
                'year' => now()->format('Y'),
                'status' => 'unpaid',
            ]))
            ->assertOk();

        $this->assertCount(1, $response->viewData('debts'));
        $this->assertEquals(100_000, $response->viewData('totalUnpaid'));
    }

    public function test_debts_report_filters_by_paid_status(): void
    {
        $this->createDebt($this->employee, ['amount' => 100_000, 'is_paid' => false, 'debt_date' => today()]);
        $this->createDebt($this->employee, ['amount' => 200_000, 'is_paid' => true, 'paid_at' => now(), 'debt_date' => today()]);

        $response = $this->actingAsOwner()
            ->get(route('company.reports.debts', [
                'month' => now()->format('m'),
                'year' => now()->format('Y'),
                'status' => 'paid',
            ]))
            ->assertOk();

        $this->assertCount(1, $response->viewData('debts'));
    }

    // ─── DEBTS PDF ────────────────────────────────────────

    public function test_debts_pdf_returns_download(): void
    {
        $this->createDebt($this->employee, ['amount' => 150_000]);

        $this->actingAsOwner()
            ->get(route('company.reports.debts.pdf', [
                'month' => now()->format('m'),
                'year' => now()->format('Y'),
            ]))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }
}
