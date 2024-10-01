<?php

namespace App\Services;

use App\Models\Question;

class QuestionService
{
    public function createQuestion($data)
    {
        return Question::create([
            'quiz_id' => $data['quiz_id'],
            'question' => $data['question'],
            'options' => json_encode($data['options']),
            'correct_answer' => $data['correct_answer'],
        ]);
    }
}
