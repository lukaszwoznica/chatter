<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $gender = Arr::random(['Male', 'Female']);

        return [
            'first_name' => $this->faker->{"firstName$gender"}(),
            'last_name' => $this->faker->{"lastName$gender"}(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'last_online_at' => now(),
            'is_online' => false,
            'is_admin' => false,
            'password' => '$2y$10$ufYzRlVUOhw.StMXBGy5T.n3anHzALB0gh8wOPiSLBl8hgzQhi5P2', // ChatterPass123
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    /**
     * Indicate that the user is administrator
     */
    public function admin(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_admin' => true
            ];
        });
    }
}
