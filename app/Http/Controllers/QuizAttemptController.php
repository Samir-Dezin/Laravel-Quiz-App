<?php

namespace App\Http\Controllers;

use App\Services\QuizService;
use Illuminate\Http\Request;

class QuizAttemptController extends Controller
{
    protected $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    public function submitQuiz(Request $request, $quizId)
    {
        return $this->quizService->attemptQuiz($request, $quizId);
    }

    public function getResults($quizId)
    {
        return $this->quizService->getQuizResults($quizId);
    }
}
