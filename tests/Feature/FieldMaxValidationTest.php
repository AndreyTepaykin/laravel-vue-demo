<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FieldMaxValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_spa_exposes_field_max_config_to_the_frontend(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('window.AppConfig = ', false);
        $response->assertSee('"password":'.config('fields.min.password'), false);
        $response->assertSee('"name":'.config('fields.max.name'), false);
        $response->assertSee('"email":'.config('fields.max.email'), false);
        $response->assertSee('"password":'.config('fields.max.password'), false);
    }

    public function test_register_rejects_password_shorter_than_configured_min(): void
    {
        $tooShort = str_repeat('a', max(config('fields.min.password') - 1, 1));

        $this->postJson('/register', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => $tooShort,
            'password_confirmation' => $tooShort,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }

    public function test_login_rejects_email_longer_than_configured_max(): void
    {
        $this->postJson('/login', [
            'email' => $this->oversizedEmail(),
            'password' => 'secret',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_rejects_password_longer_than_configured_max(): void
    {
        $user = User::factory()->create([
            'password' => 'secret',
        ]);

        $this->postJson('/login', [
            'email' => $user->email,
            'password' => str_repeat('a', config('fields.max.password') + 1),
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);

        $this->assertGuest();
    }

    public function test_register_rejects_name_longer_than_configured_max(): void
    {
        $this->postJson('/register', [
            'name' => str_repeat('a', config('fields.max.name') + 1),
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_forgot_password_rejects_email_longer_than_configured_max(): void
    {
        $this->postJson('/forgot-password', [
            'email' => $this->oversizedEmail(),
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_reset_password_rejects_token_longer_than_configured_max(): void
    {
        $this->postJson('/reset-password', [
            'token' => str_repeat('a', config('fields.max.password_reset_token') + 1),
            'email' => 'user@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['token']);
    }

    #[DataProvider('managedUserFieldProvider')]
    public function test_user_management_rejects_oversized_fields(string $field, string $valueKey): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $payload = [
            'name' => 'Valid Name',
            'email' => 'fresh@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'user',
        ];

        $payload[$valueKey] = match ($field) {
            'name' => str_repeat('a', config('fields.max.name') + 1),
            'email' => $this->oversizedEmail(),
            'password' => str_repeat('a', config('fields.max.password') + 1),
            'role' => str_repeat('a', config('fields.max.role') + 1),
        };

        if ($field === 'password') {
            $payload['password_confirmation'] = $payload['password'];
        }

        $this->actingAs($admin)
            ->postJson('/api/users', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([$field]);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function managedUserFieldProvider(): array
    {
        return [
            'name' => ['name', 'name'],
            'email' => ['email', 'email'],
            'password' => ['password', 'password'],
            'role' => ['role', 'role'],
        ];
    }

    private function oversizedEmail(): string
    {
        $max = (int) config('fields.max.email');
        $domain = '@example.com';

        return str_repeat('a', $max - strlen($domain) + 1).$domain;
    }
}
