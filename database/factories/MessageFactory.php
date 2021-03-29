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
     *
     * @return array
     */
    public function definition()
    {
        $users = User::pluck('id')->toArray();
        do {
            $sender = $this->faker->randomElement($users);
            $recipient = $this->faker->randomElement($users);
        } while ($sender === $recipient);

        return [
            'sender_id' => $sender,
            'recipient_id' => $recipient,
            'text' => $this->faker->text
        ];
    }

    /**
     * Indicate that the message has been read
     *
     * @return MessageFactory
     */
    public function read()
    {
        return $this->state(function (array $attributes) {
            return [
                'read_at' => now(),
            ];
        });
    }
}
