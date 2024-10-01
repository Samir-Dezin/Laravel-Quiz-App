<?php

namespace App\Http\Controllers;

use App\Services\QuizService;
use Illuminate\Http\Request;

class QuizAssignmentController extends Controller
{
    protected $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    public function assignQuiz(Request $request)
    {
        return $this->quizService->assignQuizToUser($request);
    }
}
