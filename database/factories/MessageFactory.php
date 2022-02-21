<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Message::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $users = User::select('id')->inRandomOrder()->take(2)->get();
        $getUserOrCreate = fn($usersCollection) => $users->shift() ?? User::factory();

        return [
            'sender_id' => $getUserOrCreate($users),
            'recipient_id' => $getUserOrCreate($users),
            'text' => $this->faker->text(rand(5, 200))
        ];
    }

    /**
     * Indicate that the message has been read
     */
    public function read(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'read_at' => now(),
            ];
        });
    }
}
