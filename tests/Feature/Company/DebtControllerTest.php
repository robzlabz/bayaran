<?php

namespace Tests\Feature\Company;

use App\Models\Debt;
use App\Models\Employee;
use App\Models\User;

class DebtControllerTest extends CompanyControllerTestCase
{
    use ControllerTestHelpers;

    // ─── ACCESS CONTROL ───────────────────────────────────

    public function test_guest_cannot_access_debts(): void
    {
        $this->get(route('company.debts.index'))
            ->assertRedirectToRoute('login');
    }

    public function test_employee_role_cannot_access_debts(): void
    {
        $this->actingAs($this->createEmployeeUser($this->owner, $this->employee))
            ->get(route('company.debts.index'))
            ->assertForbidden();
    }

    // ─── INDEX ────────────────────────────────────────────

    public function test_index_lists_owners_debts_only(): void
    {
        $this->createDebt($this->employee, ['amount' => 50_000]);

        // Another owner's debt should not appear
        $otherOwner = User::factory()->owner()->create();
        $otherEmployee = Employee::factory()->create(['owner_id' => $otherOwner->id]);
        $this->createDebt($otherEmployee, ['amount' => 999_999]);

        $this->actingAsOwner()
            ->get(route('company.debts.index'))
            ->assertOk()
            ->assertViewIs('debts.index')
            ->assertSeeText('50.000')
            ->assertDontSeeText('999.999');
    }

    public function test_index_shows_summary_totals(): void
    {
        $this->createDebt($this->employee, ['amount' => 100_000]);
        $this->createDebt($this->employee, ['amount' => 200_000, 'is_paid' => true, 'paid_at' => now()]);

        $this->actingAsOwner()
            ->get(route('company.debts.index'))
            ->assertOk()
            ->assertViewHasAll(['totalUnpaid', 'totalDebt']);
    }

    // ─── CREATE ───────────────────────────────────────────

    public function test_create_shows_active_employees(): void
    {
        $this->actingAsOwner()
            ->get(route('company.debts.create'))
            ->assertOk()
            ->assertViewIs('debts.create')
            ->assertSeeText($this->employee->name)
            ->assertDontSeeText($this->employeeInactive->name);
    }

    // ─── STORE ────────────────────────────────────────────

    public function test_store_creates_debt(): void
    {
        $this->actingAsOwner()
            ->post(route('company.debts.store'), [
                'employee_id' => $this->employee->id,
                'amount' => 150_000,
                'description' => 'Pinjaman transport',
                'debt_date' => today()->format('Y-m-d'),
            ])
            ->assertRedirectToRoute('company.debts.index')
            ;

        $this->assertDatabaseHas('debts', [
            'employee_id' => $this->employee->id,
            'amount' => 150_000,
            'is_paid' => false,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAsOwner()
            ->post(route('company.debts.store'), [])
            ->assertSessionHasErrors(['employee_id', 'amount', 'debt_date']);
    }

    public function test_store_prevents_debt_for_other_owners_employee(): void
    {
        $otherOwner = User::factory()->owner()->create();
        $otherEmployee = Employee::factory()->create(['owner_id' => $otherOwner->id]);

        $this->actingAsOwner()
            ->post(route('company.debts.store'), [
                'employee_id' => $otherEmployee->id,
                'amount' => 50_000,
                'debt_date' => today()->format('Y-m-d'),
            ])
            ->assertForbidden();
    }

    // ─── EDIT ─────────────────────────────────────────────

    public function test_edit_shows_debt_form(): void
    {
        $debt = $this->createDebt($this->employee);

        $this->actingAsOwner()
            ->get(route('company.debts.edit', $debt))
            ->assertOk()
            ->assertViewIs('debts.edit')
            ->assertViewHasAll(['debt', 'employees']);
    }

    public function test_edit_prevents_other_owners_debt(): void
    {
        $otherOwner = User::factory()->owner()->create();
        $otherEmployee = Employee::factory()->create(['owner_id' => $otherOwner->id]);
        $debt = $this->createDebt($otherEmployee);

        $this->actingAsOwner()
            ->get(route('company.debts.edit', $debt))
            ->assertForbidden();
    }

    // ─── UPDATE ───────────────────────────────────────────

    public function test_update_changes_debt(): void
    {
        $debt = $this->createDebt($this->employee, ['amount' => 100_000]);

        $this->actingAsOwner()
            ->put(route('company.debts.update', $debt), [
                'employee_id' => $this->employee->id,
                'amount' => 200_000,
                'description' => 'Updated',
                'debt_date' => today()->format('Y-m-d'),
                'is_paid' => true,
            ])
            ->assertRedirectToRoute('company.debts.index')
            ;

        $this->assertDatabaseHas('debts', [
            'id' => $debt->id,
            'amount' => 200_000,
            'is_paid' => true,
        ]);
    }

    public function test_update_can_mark_as_unpaid_again(): void
    {
        $debt = $this->createDebt($this->employee, ['amount' => 100_000, 'is_paid' => true, 'paid_at' => now()]);

        $this->actingAsOwner()
            ->put(route('company.debts.update', $debt), [
                'employee_id' => $this->employee->id,
                'amount' => 100_000,
                'description' => 'Reopened',
                'debt_date' => today()->format('Y-m-d'),
                'is_paid' => false,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('debts', [
            'id' => $debt->id,
            'is_paid' => false,
            'paid_at' => null,
        ]);
    }

    public function test_update_prevents_other_owners_debt(): void
    {
        $otherOwner = User::factory()->owner()->create();
        $otherEmployee = Employee::factory()->create(['owner_id' => $otherOwner->id]);
        $debt = $this->createDebt($otherEmployee);

        $this->actingAsOwner()
            ->put(route('company.debts.update', $debt), [
                'employee_id' => $otherEmployee->id,
                'amount' => 999,
                'debt_date' => today()->format('Y-m-d'),
            ])
            ->assertForbidden();
    }

    // ─── DESTROY ──────────────────────────────────────────

    public function test_destroy_deletes_debt(): void
    {
        $debt = $this->createDebt($this->employee);

        $this->actingAsOwner()
            ->delete(route('company.debts.destroy', $debt))
            ->assertRedirectToRoute('company.debts.index')
            ;

        $this->assertDatabaseMissing('debts', ['id' => $debt->id]);
    }

    public function test_destroy_prevents_other_owners_debt(): void
    {
        $otherOwner = User::factory()->owner()->create();
        $otherEmployee = Employee::factory()->create(['owner_id' => $otherOwner->id]);
        $debt = $this->createDebt($otherEmployee);

        $this->actingAsOwner()
            ->delete(route('company.debts.destroy', $debt))
            ->assertForbidden();
    }

    // ─── PAY ──────────────────────────────────────────────

    public function test_pay_marks_debt_as_paid(): void
    {
        $debt = $this->createDebt($this->employee, ['amount' => 75_000]);

        $this->actingAsOwner()
            ->patch(route('company.debts.pay', $debt))
            ->assertRedirectToRoute('company.debts.index')
            ;

        $this->assertDatabaseHas('debts', [
            'id' => $debt->id,
            'is_paid' => true,
        ]);
        $this->assertNotNull($debt->fresh()->paid_at);
    }

    public function test_pay_prevents_other_owners_debt(): void
    {
        $otherOwner = User::factory()->owner()->create();
        $otherEmployee = Employee::factory()->create(['owner_id' => $otherOwner->id]);
        $debt = $this->createDebt($otherEmployee);

        $this->actingAsOwner()
            ->patch(route('company.debts.pay', $debt))
            ->assertForbidden();
    }
}
