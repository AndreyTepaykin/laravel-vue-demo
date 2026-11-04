<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ProtectedUserRestrictionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_protected_user_cannot_be_updated(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $protected = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@jsonapi.com',
        ]);
        $protected->assignRole('admin');

        $this->actingAs($admin)
            ->putJson('/api/users/'.$protected->id, [
                'name' => 'Changed Name',
                'email' => $protected->email,
                'role' => 'admin',
            ])
            ->assertForbidden()
            ->assertJsonPath('message', "Sorry you can't manage data on demo app");

        $this->assertSame('Admin', $protected->fresh()->name);
    }

    public function test_protected_user_cannot_be_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $protected = User::factory()->create([
            'email' => 'test@example.com',
        ]);
        $protected->assignRole('user');

        $this->actingAs($admin)
            ->deleteJson('/api/users/'.$protected->id)
            ->assertForbidden()
            ->assertJsonPath('message', "Sorry you can't manage data on demo app");

        $this->assertDatabaseHas('users', ['id' => $protected->id]);
    }

    public function test_protected_user_cannot_update_own_profile(): void
    {
        $protected = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $protected->assignRole('user');

        $this->actingAs($protected)
            ->putJson('/api/user', [
                'name' => 'Changed Name',
                'email' => $protected->email,
            ])
            ->assertForbidden()
            ->assertJsonPath('message', "Sorry you can't manage data on demo app");

        $this->assertSame('Test User', $protected->fresh()->name);
    }

    public function test_protected_user_password_cannot_be_reset(): void
    {
        $protected = User::factory()->create([
            'email' => 'admin@jsonapi.com',
            'password' => Hash::make('secret'),
        ]);

        $token = Password::createToken($protected);

        $this->postJson('/reset-password', [
            'email' => $protected->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
            'token' => $token,
        ])
            ->assertForbidden()
            ->assertJsonPath('message', "Sorry you can't manage data on demo app");

        $this->assertTrue(Hash::check('secret', $protected->fresh()->password));
    }

    public function test_protected_user_does_not_receive_password_reset_link(): void
    {
        Notification::fake();

        $protected = User::factory()->create([
            'email' => 'admin@jsonapi.com',
        ]);

        $this->postJson('/forgot-password', [
            'email' => $protected->email,
        ])
            ->assertOk()
            ->assertJsonPath('message', __(Password::RESET_LINK_SENT));

        Notification::assertNothingSent();
    }

    public function test_non_protected_user_can_be_updated_and_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create(['name' => 'Original']);
        $user->assignRole('user');

        $this->actingAs($admin)
            ->putJson('/api/users/'.$user->id, [
                'name' => 'Changed Name',
                'email' => $user->email,
                'role' => 'user',
            ])
            ->assertOk();

        $this->assertSame('Changed Name', $user->fresh()->name);

        $this->actingAs($admin)
            ->deleteJson('/api/users/'.$user->id)
            ->assertNoContent();

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
