
<?php
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run()
    {
        Quiz::factory()
            ->count(10) // Adjust the number of quizzes as needed
            ->create()
            ->each(function ($quiz) {
                $questions = Question::factory()->count(5)->create(['quiz_id' => $quiz->id]); // 5 questions per quiz
            });
    }
}
