<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Outlet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OutletTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed default outlet (Main Outlet) which is required for DB constraints and active_outlet logic
        Outlet::create([
            'id' => 1,
            'name' => 'Main Outlet',
            'location' => 'HQ',
            'contact_info' => '0712345678',
            'is_active' => true
        ]);
    }

    public function test_admin_can_view_outlets_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/outlets');

        $response->assertStatus(200);
        $response->assertSee('Main Outlet');
    }

    public function test_employee_cannot_view_outlets_list(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);

        $response = $this->actingAs($employee)->get('/admin/outlets');

        $response->assertStatus(302); // Redirect back / home because of admin middleware
    }

    public function test_admin_can_create_outlet(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/outlets', [
            'name' => 'New Branch Store',
            'location' => 'Mombasa',
            'contact_info' => '0711223344',
            'is_active' => '1'
        ]);

        $response->assertRedirect('/admin/outlets');
        $this->assertDatabaseHas('outlets', ['name' => 'New Branch Store']);
    }

    public function test_admin_can_update_outlet(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $outlet = Outlet::create([
            'name' => 'Old Branch Store',
            'location' => 'Kisumu',
            'contact_info' => '0755667788',
            'is_active' => true
        ]);

        $response = $this->actingAs($admin)->put("/admin/outlets/{$outlet->id}", [
            'name' => 'Updated Branch Store',
            'location' => 'Kisumu Central',
            'contact_info' => '0755667788',
            'is_active' => '1'
        ]);

        $response->assertRedirect('/admin/outlets');
        $this->assertDatabaseHas('outlets', [
            'id' => $outlet->id,
            'name' => 'Updated Branch Store',
            'location' => 'Kisumu Central'
        ]);
    }

    public function test_admin_cannot_delete_default_main_outlet(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->delete('/admin/outlets/1');

        $response->assertRedirect('/admin/outlets');
        $response->assertSessionHas('error', 'Cannot delete the Main Outlet (Default HQ).');
        $this->assertDatabaseHas('outlets', ['id' => 1]);
    }

    public function test_admin_cannot_delete_currently_active_outlet(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $outlet = Outlet::create([
            'name' => 'Temp Branch',
            'is_active' => true
        ]);

        // Switched to Temp Branch in session
        $response = $this->actingAs($admin)
            ->withSession(['active_outlet_id' => $outlet->id])
            ->delete("/admin/outlets/{$outlet->id}");

        $response->assertRedirect('/admin/outlets');
        $response->assertSessionHas('error', 'Cannot delete the outlet you are currently switched to. Switch to another outlet first.');
        $this->assertDatabaseHas('outlets', ['id' => $outlet->id]);
    }

    public function test_admin_cannot_delete_outlet_with_assigned_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $outlet = Outlet::create([
            'name' => 'Branch With User',
            'is_active' => true
        ]);

        // Create a user assigned to this outlet
        User::factory()->create([
            'role' => 'employee',
            'outlet_id' => $outlet->id
        ]);

        $response = $this->actingAs($admin)->delete("/admin/outlets/{$outlet->id}");

        $response->assertRedirect('/admin/outlets');
        $response->assertSessionHas('error', 'Cannot delete outlet with assigned users. Reassign or delete users first.');
        $this->assertDatabaseHas('outlets', ['id' => $outlet->id]);
    }
}
