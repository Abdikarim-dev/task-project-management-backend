<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /** @var list<string> */
    private static array $somaliNames = [
        'Hassan Abdi',
        'Amina Mohamed',
        'Ibrahim Hashi',
        'Khadija Osman',
        'Mohamed Farah',
        'Fadumo Ali',
        'Yusuf Nur',
        'Sahra Abdi',
        'Abdirahman Warsame',
        'Hibo Hassan',
    ];

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(self::$somaliNames),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => UserRole::Staff,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (): array => [
            'role' => UserRole::Admin,
        ]);
    }

    public function staff(): static
    {
        return $this->state(fn (): array => [
            'role' => UserRole::Staff,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (): array => [
            'email_verified_at' => null,
        ]);
    }
}
