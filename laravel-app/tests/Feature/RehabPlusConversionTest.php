<?php

namespace Tests\Feature;

use Tests\TestCase;

class RehabPlusConversionTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    public function test_login_page_loads(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('Login');
    }

    public function test_original_admin_management_pages_load(): void
    {
        $this->actingAs(
            \App\Models\User::factory()->create([
                'name' => 'Super Admin',
                'role' => 'superadmin',
                'is_active' => true,
            ])
        );

        $pages = [
            '/reports' => 'Reports & Analytics',
            '/patients' => 'Patients',
            '/appointments' => 'Appointments',
            '/billing' => 'Billing and Payment Records',
            '/inventory' => 'Inventory and Supplies',
            '/schedule' => 'Staff Schedule',
            '/notes' => 'Therapy Notes & Care Plans',
            '/assessments' => 'Assessments & Goals',
            '/users' => 'Roles & Patient Accounts',
        ];

        foreach ($pages as $path => $heading) {
            $this->get($path)
                ->assertOk()
                ->assertSee($heading)
                ->assertSee('RehabPlus');
        }
    }

    public function test_users_page_contains_patient_portal_accounts(): void
    {
        $this->actingAs(
            \App\Models\User::factory()->create([
                'name' => 'Super Admin',
                'role' => 'superadmin',
                'is_active' => true,
            ])
        );

        $response = $this->get('/users');

        $response->assertOk()
            ->assertSee('Patient Portal Accounts')
            ->assertSee('Create Patient Account')
            ->assertSee('Patient Full Name')
            ->assertSee('Temporary Password');
    }

    public function test_billing_page_uses_pesos(): void
    {
        $this->actingAs(
            \App\Models\User::factory()->create([
                'name' => 'Super Admin',
                'role' => 'superadmin',
                'is_active' => true,
            ])
        );

        $response = $this->get('/billing');

        $response->assertOk()
            ->assertSee('₱0.00')
            ->assertSee('₱0.00');
    }

    public function test_payment_record_can_be_created(): void
    {
        $this->actingAs(
            \App\Models\User::factory()->create([
                'name' => 'Super Admin',
                'role' => 'superadmin',
                'is_active' => true,
            ])
        );

        $response = $this->post('/billing', [
            'patient' => 'Maria Santos',
            'session' => 'Physical Therapy',
            'fee' => '1500.00',
            'status' => 'Paid',
            'date' => '2026-10-09',
        ]);

        $response->assertRedirect('/billing');
        $this->assertDatabaseHas('payment_records', [
            'patient' => 'Maria Santos',
            'session' => 'Physical Therapy',
            'fee' => '1500.00',
            'status' => 'Paid',
        ]);
    }

    public function test_inventory_item_can_be_created(): void
    {
        $this->actingAs(
            \App\Models\User::factory()->create([
                'name' => 'Super Admin',
                'role' => 'superadmin',
                'is_active' => true,
            ])
        );

        $response = $this->post('/inventory', [
            'item' => 'Resistance Band',
            'category' => 'Equipment',
            'stock' => 20,
            'reorder' => 10,
            'status' => 'Healthy',
        ]);

        $response->assertRedirect('/inventory');
        $this->assertDatabaseHas('inventory_items', [
            'item' => 'Resistance Band',
            'category' => 'Equipment',
            'stock' => 20,
            'reorder' => 10,
            'status' => 'Healthy',
        ]);
    }

    public function test_user_can_log_out(): void
    {
        $user = \App\Models\User::factory()->create([
            'name' => 'Super Admin',
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $this->actingAs($user);
        $originalSessionId = session()->getId();
        $originalCsrfToken = csrf_token();

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
        $this->assertNotSame($originalSessionId, session()->getId());
        $this->assertNotSame($originalCsrfToken, csrf_token());
    }
}
