<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * Demo accounts that must not be updated or deleted.
     *
     * @var list<string>
     */
    public const PROTECTED_EMAILS = [
        'admin@jsonapi.com',
        'test@example.com',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Register model event hooks.
     */
    protected static function booted(): void
    {
        static::created(function (): void {
            static::pruneToMaxUsers();
        });
    }

    /**
     * Determine whether the given email belongs to a protected demo account.
     */
    public static function isProtectedEmail(string $email): bool
    {
        return in_array(Str::lower($email), self::PROTECTED_EMAILS, true);
    }

    /**
     * Determine whether this user is a protected demo account.
     */
    public function isProtected(): bool
    {
        return self::isProtectedEmail($this->email);
    }

    /**
     * Delete the oldest non-protected users until the configured max is met.
     */
    public static function pruneToMaxUsers(): void
    {
        $max = (int) config('users.max');

        if ($max < 1) {
            return;
        }

        DB::transaction(function () use ($max): void {
            $excess = static::query()->lockForUpdate()->count() - $max;

            if ($excess <= 0) {
                return;
            }

            $victims = static::query()
                ->whereNotIn('email', self::PROTECTED_EMAILS)
                ->oldest()
                ->orderBy('id')
                ->limit($excess)
                ->lockForUpdate()
                ->get();

            foreach ($victims as $victim) {
                $victim->delete();
            }
        });
    }
}
