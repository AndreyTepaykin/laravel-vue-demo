<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_list_users(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        User::factory()->create()->assignRole('user');

        $this->actingAs($admin)
            ->getJson('/api/users')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_regular_user_cannot_list_users(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)
            ->getJson('/api/users')
            ->assertForbidden();
    }

    public function test_admin_can_create_user_with_role(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->postJson('/api/users', [
            'name' => 'Managed User',
            'email' => 'managed@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'user',
        ]);

        $response->assertCreated()
            ->assertJsonPath('user.email', 'managed@example.com')
            ->assertJsonPath('user.roles.0', 'user');

        $this->assertDatabaseHas('users', ['email' => 'managed@example.com']);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->deleteJson('/api/users/'.$admin->id)
            ->assertForbidden();
    }

    public function test_admin_can_delete_another_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($admin)
            ->deleteJson('/api/users/'.$user->id)
            ->assertNoContent();

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
