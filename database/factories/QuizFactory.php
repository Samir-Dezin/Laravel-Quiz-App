<?php

namespace Database\Factories;

use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizFactory extends Factory
{
    protected $model = Quiz::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'activation_time' => now()->addMinutes(2), // Activate after 2 minutes
            'expiration_time' => now()->addHours(24), // Expire after 24 hours
        ];
    }
}
