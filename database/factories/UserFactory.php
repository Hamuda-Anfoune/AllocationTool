<?php

namespace Database\Factories;

use App\Models\UniversityUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'name' => fake()->name(),
            'account_type_id' => '004',
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'active' => true,
        ];
    }

    /**
     * Ensure the matching university_users row exists before the user is persisted,
     * since users.email carries a NOT NULL foreign key to university_users.email.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (User $user) {
            UniversityUser::firstOrCreate(
                ['email' => $user->email],
                ['account_type_id' => $user->account_type_id],
            );
        });
    }
}
