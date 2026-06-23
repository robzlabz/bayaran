<?php

namespace Tests\Feature\Company;

use App\Models\Employee;
use App\Models\User;

class TransactionControllerTest extends CompanyControllerTestCase
{
    use ControllerTestHelpers;

    // ─── ACCESS CONTROL ───────────────────────────────────

    public function test_guest_cannot_access_transactions(): void
    {
        $this->get(route('company.transactions.index'))
            ->assertRedirectToRoute('login');
    }

    public function test_employee_role_cannot_access_transactions(): void
    {
        $this->actingAs($this->createEmployeeUser($this->owner, $this->employee))
            ->get(route('company.transactions.index'))
            ->assertForbidden();
    }

    // ─── INDEX ────────────────────────────────────────────

    public function test_index_lists_transactions(): void
    {
        $this->createTopup($this->employee, 200_000);

        $this->actingAsOwner()
            ->get(route('company.transactions.index'))
            ->assertOk()
            ->assertViewIs('transactions.index')
            ->assertViewHas('transactions');
    }

    public function test_index_filters_by_employee(): void
    {
        $employee2 = Employee::factory()->daily()->create(['owner_id' => $this->owner->id]);
        $this->createTopup($this->employee, 100_000);
        $this->createTopup($employee2, 200_000);

        $response = $this->actingAsOwner()
            ->get(route('company.transactions.index', ['employee_id' => $this->employee->id]))
            ->assertOk();

        $transactions = $response->viewData('transactions');
        $this->assertCount(1, $transactions);
        $this->assertEquals(100_000, $transactions->first()->amount);
    }

    public function test_index_prevents_seeing_other_owners_transactions(): void
    {
        $otherOwner = User::factory()->owner()->create();
        $otherEmployee = Employee::factory()->create(['owner_id' => $otherOwner->id]);
        $this->createTopup($otherEmployee, 999_999);

        $this->actingAsOwner()
            ->get(route('company.transactions.index'))
            ->assertOk()
            ->assertDontSeeText('999.999');
    }

    // ─── TOPUP ────────────────────────────────────────────

    public function test_create_topup_shows_active_employees(): void
    {
        $this->actingAsOwner()
            ->get(route('company.transactions.topup'))
            ->assertOk()
            ->assertViewIs('transactions.topup')
            ->assertSeeText($this->employee->name)
            ->assertDontSeeText($this->employeeInactive->name);
    }

    public function test_store_topup_increases_employee_balance(): void
    {
        $this->actingAsOwner()
            ->post(route('company.transactions.topup.store'), [
                'employee_id' => $this->employee->id,
                'amount' => 500_000,
                'description' => 'Isi saldo awal',
            ])
            ->assertRedirectToRoute('company.transactions.index')
            ;

        $this->assertDatabaseHas('transactions', [
            'employee_id' => $this->employee->id,
            'type' => 'topup',
            'amount' => 500_000,
            'balance_before' => 0,
            'balance_after' => 500_000,
        ]);

        $this->assertEquals(500_000, $this->employee->fresh()->balance);
    }

    public function test_store_topup_validates_required_fields(): void
    {
        $this->actingAsOwner()
            ->post(route('company.transactions.topup.store'), [])
            ->assertSessionHasErrors(['employee_id', 'amount']);
    }

    public function test_store_topup_prevents_other_owners_employee(): void
    {
        $otherOwner = User::factory()->owner()->create();
        $otherEmployee = Employee::factory()->create(['owner_id' => $otherOwner->id]);

        $this->actingAsOwner()
            ->post(route('company.transactions.topup.store'), [
                'employee_id' => $otherEmployee->id,
                'amount' => 100_000,
            ])
            ->assertForbidden();
    }

    // ─── TRANSPORT ────────────────────────────────────────

    public function test_create_transport_shows_active_employees(): void
    {
        $this->actingAsOwner()
            ->get(route('company.transports.create'))
            ->assertOk()
            ->assertViewIs('transports.create')
            ->assertSeeText($this->employee->name);
    }

    public function test_store_transport_decreases_employee_balance(): void
    {
        // First topup so employee has balance
        $this->createTopup($this->employee, 300_000);

        $this->actingAsOwner()
            ->post(route('company.transports.store'), [
                'employee_id' => $this->employee->id,
                'amount' => 50_000,
                'description' => 'Ongkos ke gudang',
            ])
            ->assertRedirectToRoute('company.transactions.index')
            ;

        $this->assertDatabaseHas('transactions', [
            'employee_id' => $this->employee->id,
            'type' => 'transport',
            'amount' => -50_000,
            'balance_before' => 300_000,
            'balance_after' => 250_000,
        ]);

        $this->assertEquals(250_000, $this->employee->fresh()->balance);
    }

    public function test_store_transport_validates_amount(): void
    {
        $this->actingAsOwner()
            ->post(route('company.transports.store'), [
                'employee_id' => $this->employee->id,
                'amount' => 0,
            ])
            ->assertSessionHasErrors(['amount']);
    }

    public function test_store_transport_prevents_other_owners_employee(): void
    {
        $otherOwner = User::factory()->owner()->create();
        $otherEmployee = Employee::factory()->create(['owner_id' => $otherOwner->id]);

        $this->actingAsOwner()
            ->post(route('company.transports.store'), [
                'employee_id' => $otherEmployee->id,
                'amount' => 20_000,
            ])
            ->assertForbidden();
    }
}
