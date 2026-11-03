<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@jsonapi.com'],
            [
                'name' => 'Admin',
                'password' => 'secret',
                'email_verified_at' => now(),
            ],
        );
        $admin->syncRoles(['admin']);

        $user = User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
                'email_verified_at' => now(),
            ],
        );
        $user->syncRoles(['user']);
    }
}
