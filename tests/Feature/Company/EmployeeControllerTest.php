<?php

namespace Tests\Feature\Company;

use App\Models\Employee;
use App\Models\User;

class EmployeeControllerTest extends CompanyControllerTestCase
{
    use ControllerTestHelpers;

    // ─── ACCESS CONTROL ───────────────────────────────────

    public function test_guest_cannot_access_employees(): void
    {
        $this->get(route('company.employees.index'))
            ->assertRedirectToRoute('login');
    }

    public function test_employee_role_cannot_access_employees(): void
    {
        $employeeUser = $this->createEmployeeUser($this->owner, $this->employee);

        $this->actingAs($employeeUser)
            ->get(route('company.employees.index'))
            ->assertForbidden();
    }

    // ─── INDEX ────────────────────────────────────────────

    public function test_index_lists_owners_employees_only(): void
    {
        // Another owner's employee should not appear
        $otherOwner = User::factory()->owner()->create();
        Employee::factory()->create(['owner_id' => $otherOwner->id]);

        $this->actingAsOwner()
            ->get(route('company.employees.index'))
            ->assertOk()
            ->assertViewIs('employees.index')
            ->assertSeeText($this->employee->name)
            ->assertDontSeeText(Employee::where('owner_id', $otherOwner->id)->first()->name);
    }

    public function test_index_paginates_employees(): void
    {
        Employee::factory()->monthly()->count(25)->create([
            'owner_id' => $this->owner->id,
        ]);

        $this->actingAsOwner()
            ->get(route('company.employees.index'))
            ->assertOk()
            ->assertViewHas('employees');
    }

    // ─── CREATE ───────────────────────────────────────────

    public function test_create_returns_view(): void
    {
        $this->actingAsOwner()
            ->get(route('company.employees.create'))
            ->assertOk()
            ->assertViewIs('employees.create');
    }

    // ─── STORE ────────────────────────────────────────────

    public function test_store_creates_employee_and_user_account(): void
    {
        $payload = [
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'payment_type' => 'monthly',
            'salary_amount' => 5_000_000,
        ];

        $this->actingAsOwner()
            ->post(route('company.employees.store'), $payload)
            ->assertRedirectToRoute('company.employees.index')
            ;

        $this->assertDatabaseHas('employees', [
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'owner_id' => $this->owner->id,
        ]);

        // Auto-created user account
        $this->assertDatabaseHas('users', [
            'phone' => '081234567890',
            'role' => 'employee',
            'owner_id' => $this->owner->id,
        ]);
    }

    public function test_store_with_daily_payment_type(): void
    {
        $payload = [
            'name' => 'Asep daily',
            'phone' => '081111111111',
            'payment_type' => 'daily',
            'daily_rate' => 200_000,
        ];

        $this->actingAsOwner()
            ->post(route('company.employees.store'), $payload)
            ->assertRedirectToRoute('company.employees.index')
            ;

        $this->assertDatabaseHas('employees', [
            'phone' => '081111111111',
            'payment_type' => 'daily',
            'daily_rate' => 200_000,
        ]);
    }

    public function test_store_with_hourly_payment_type(): void
    {
        $payload = [
            'name' => 'Cici hourly',
            'phone' => '082222222222',
            'payment_type' => 'hourly',
            'hourly_rate' => 30_000,
        ];

        $this->actingAsOwner()
            ->post(route('company.employees.store'), $payload)
            ->assertRedirectToRoute('company.employees.index');

        $this->assertDatabaseHas('employees', [
            'phone' => '082222222222',
            'payment_type' => 'hourly',
            'hourly_rate' => 30_000,
        ]);
    }

    public function test_store_with_per_delivery_payment_type(): void
    {
        $payload = [
            'name' => 'Dodi kurir',
            'phone' => '083333333333',
            'payment_type' => 'per_delivery',
            'delivery_rate' => 15_000,
        ];

        $this->actingAsOwner()
            ->post(route('company.employees.store'), $payload)
            ->assertRedirectToRoute('company.employees.index');

        $this->assertDatabaseHas('employees', [
            'phone' => '083333333333',
            'payment_type' => 'per_delivery',
            'delivery_rate' => 15_000,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAsOwner()
            ->post(route('company.employees.store'), [])
            ->assertSessionHasErrors(['name', 'phone', 'payment_type']);
    }

    public function test_store_validates_duplicate_phone(): void
    {
        $payload = [
            'name' => 'Duplicate',
            'phone' => $this->employee->phone,
            'payment_type' => 'monthly',
            'salary_amount' => 3_000_000,
        ];

        $this->actingAsOwner()
            ->post(route('company.employees.store'), $payload)
            ->assertSessionHasErrors(['phone']);
    }

    // ─── SHOW ─────────────────────────────────────────────

    public function test_show_displays_employee(): void
    {
        $this->actingAsOwner()
            ->get(route('company.employees.show', $this->employee))
            ->assertOk()
            ->assertViewIs('employees.show')
            ->assertSeeText($this->employee->name);
    }

    public function test_show_prevents_access_to_other_owners_employee(): void
    {
        $otherOwner = User::factory()->owner()->create();
        $otherEmployee = Employee::factory()->create(['owner_id' => $otherOwner->id]);

        $this->actingAsOwner()
            ->get(route('company.employees.show', $otherEmployee))
            ->assertForbidden();
    }

    // ─── EDIT ─────────────────────────────────────────────

    public function test_edit_returns_view(): void
    {
        $this->actingAsOwner()
            ->get(route('company.employees.edit', $this->employee))
            ->assertOk()
            ->assertViewIs('employees.edit')
            ->assertSee($this->employee->name);
    }

    public function test_edit_prevents_access_to_other_owners_employee(): void
    {
        $otherOwner = User::factory()->owner()->create();
        $otherEmployee = Employee::factory()->create(['owner_id' => $otherOwner->id]);

        $this->actingAsOwner()
            ->get(route('company.employees.edit', $otherEmployee))
            ->assertForbidden();
    }

    // ─── UPDATE ───────────────────────────────────────────

    public function test_update_changes_employee(): void
    {
        $this->actingAsOwner()
            ->put(route('company.employees.update', $this->employee), [
                'name' => 'Nama Baru',
                'phone' => '089999999999',
                'payment_type' => 'daily',
                'daily_rate' => 250_000,
                'is_active' => true,
            ])
            ->assertRedirectToRoute('company.employees.index')
            ;

        $this->assertDatabaseHas('employees', [
            'id' => $this->employee->id,
            'name' => 'Nama Baru',
            'payment_type' => 'daily',
        ]);
    }

    public function test_update_also_updates_related_user(): void
    {
        $employeeUser = $this->createEmployeeUser($this->owner, $this->employee);

        $this->actingAsOwner()
            ->put(route('company.employees.update', $this->employee), [
                'name' => 'Updated Name',
                'phone' => '087777777777',
                'payment_type' => 'monthly',
                'salary_amount' => 6_000_000,
                'is_active' => true,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $employeeUser->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_update_prevents_other_owners_employee(): void
    {
        $otherOwner = User::factory()->owner()->create();
        $otherEmployee = Employee::factory()->create(['owner_id' => $otherOwner->id]);

        $this->actingAsOwner()
            ->put(route('company.employees.update', $otherEmployee), [
                'name' => 'Hack Attempt',
                'phone' => '080000000000',
                'payment_type' => 'monthly',
                'salary_amount' => 1_000_000,
                'is_active' => true,
            ])
            ->assertForbidden();
    }

    // ─── DESTROY ──────────────────────────────────────────

    public function test_destroy_deletes_employee(): void
    {
        $this->actingAsOwner()
            ->delete(route('company.employees.destroy', $this->employee))
            ->assertRedirectToRoute('company.employees.index')
            ;

        $this->assertDatabaseMissing('employees', ['id' => $this->employee->id]);
    }

    public function test_destroy_prevents_deleting_other_owners_employee(): void
    {
        $otherOwner = User::factory()->owner()->create();
        $otherEmployee = Employee::factory()->create(['owner_id' => $otherOwner->id]);

        $this->actingAsOwner()
            ->delete(route('company.employees.destroy', $otherEmployee))
            ->assertForbidden();
    }
}
