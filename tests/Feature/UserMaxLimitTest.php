<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UserMaxLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_spa_exposes_users_max_config_to_the_frontend(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('window.AppConfig = ', false);
        $response->assertSee('"users":{"max":'.config('users.max').'}', false);
    }

    public function test_creating_a_user_deletes_the_oldest_non_protected_user_when_over_max(): void
    {
        config(['users.max' => 3]);

        $oldest = User::factory()->create([
            'email' => 'oldest@example.com',
            'created_at' => Carbon::now()->subDays(3),
        ]);
        $oldest->assignRole('user');

        $middle = User::factory()->create([
            'email' => 'middle@example.com',
            'created_at' => Carbon::now()->subDays(2),
        ]);
        $middle->assignRole('user');

        $admin = User::factory()->create([
            'email' => 'admin-manager@example.com',
            'created_at' => Carbon::now()->subDay(),
        ]);
        $admin->assignRole('admin');

        $this->assertSame(3, User::query()->count());

        $this->actingAs($admin)->postJson('/api/users', [
            'name' => 'Newest User',
            'email' => 'newest@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'user',
        ])->assertCreated();

        $this->assertSame(3, User::query()->count());
        $this->assertDatabaseMissing('users', ['id' => $oldest->id]);
        $this->assertDatabaseHas('users', ['email' => 'middle@example.com']);
        $this->assertDatabaseHas('users', ['email' => 'newest@example.com']);
    }

    public function test_overflow_never_deletes_protected_users(): void
    {
        config(['users.max' => 100]);

        $protectedAdmin = User::factory()->create([
            'email' => 'admin@jsonapi.com',
            'created_at' => Carbon::now()->subDays(5),
        ]);
        $protectedAdmin->assignRole('admin');

        $protectedTest = User::factory()->create([
            'email' => 'test@example.com',
            'created_at' => Carbon::now()->subDays(4),
        ]);
        $protectedTest->assignRole('user');

        $oldestRemovable = User::factory()->create([
            'email' => 'removable@example.com',
            'created_at' => Carbon::now()->subDays(3),
        ]);
        $oldestRemovable->assignRole('user');

        config(['users.max' => 3]);

        $this->actingAs($protectedAdmin)->postJson('/api/users', [
            'name' => 'Newest User',
            'email' => 'newest@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'user',
        ])->assertCreated();

        $this->assertDatabaseHas('users', ['email' => 'admin@jsonapi.com']);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        $this->assertDatabaseMissing('users', ['id' => $oldestRemovable->id]);
        $this->assertDatabaseHas('users', ['email' => 'newest@example.com']);
        $this->assertSame(3, User::query()->count());
    }

    public function test_registration_also_prunes_oldest_non_protected_user_when_over_max(): void
    {
        config(['users.max' => 2]);

        $oldest = User::factory()->create([
            'email' => 'oldest@example.com',
            'created_at' => Carbon::now()->subDay(),
        ]);
        $oldest->assignRole('user');

        User::factory()->create([
            'email' => 'keeper@example.com',
            'created_at' => Carbon::now()->subHour(),
        ])->assignRole('user');

        $this->postJson('/register', [
            'name' => 'Registered User',
            'email' => 'registered@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertCreated();

        $this->assertSame(2, User::query()->count());
        $this->assertDatabaseMissing('users', ['id' => $oldest->id]);
        $this->assertDatabaseHas('users', ['email' => 'registered@example.com']);
    }
}
