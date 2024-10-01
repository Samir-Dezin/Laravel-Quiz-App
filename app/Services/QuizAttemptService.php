<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class QuizAttemptService
{
    public function submitQuizAttempt(Request $request, $quizId)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer' => 'required|string',
        ]);

        $quiz = Quiz::findOrFail($quizId);

        // Store the quiz attempt
        $attempt = QuizAttempt::create([
            'user_id' => auth()->id(),
            'quiz_id' => $quiz->id,
            'answers' => json_encode($request->answers),
        ]);

        return response()->json(['message' => 'Quiz attempt submitted successfully', 'attempt' => $attempt], 201);
    }
}
