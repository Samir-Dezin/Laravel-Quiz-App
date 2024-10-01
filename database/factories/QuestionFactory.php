
<?php
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition()
    {
        // Generate 4 random unique options
        $options = $this->faker->unique()->words(4);

        // Randomly select one option as the correct answer
        $correct_answer = $this->faker->randomElement($options);

        return [
            'quiz_id' => \App\Models\Quiz::factory(), // Make sure to create a quiz first
            'question' => $this->faker->sentence,
            'options' => json_encode($options), // Store the options as a JSON array
            'correct_answer' => $correct_answer, // Ensure the correct answer is one of the options
        ];
    }
}
