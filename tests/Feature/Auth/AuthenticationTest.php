<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_users_can_authenticate_using_the_login_endpoint(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@jsonapi.com',
            'password' => 'secret',
        ]);
        $user->assignRole('admin');

        $response = $this->postJson('/login', [
            'email' => 'admin@jsonapi.com',
            'password' => 'secret',
        ]);

        $response->assertOk()
            ->assertJsonPath('user.email', 'admin@jsonapi.com')
            ->assertJsonPath('user.roles.0', 'admin');

        $this->assertAuthenticated();
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@jsonapi.com',
            'password' => 'secret',
        ]);

        $response = $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);

        $this->assertGuest();
    }

    public function test_users_can_register_and_receive_the_user_role(): void
    {
        $response = $this->postJson('/register', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertCreated()
            ->assertJsonPath('user.email', 'new@example.com')
            ->assertJsonPath('user.roles.0', 'user');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
    }

    public function test_authenticated_users_can_fetch_their_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->getJson('/api/user');

        $response->assertOk()
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonPath('data.roles.0', 'user');
    }

    public function test_authenticated_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/logout');

        $response->assertOk();
        $this->assertGuest();
    }

    public function test_guests_can_call_logout_idempotently(): void
    {
        $this->postJson('/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Logged out.');

        $this->assertGuest();
    }

    public function test_guests_cannot_fetch_their_profile(): void
    {
        $this->getJson('/api/user')->assertUnauthorized();
    }
}
