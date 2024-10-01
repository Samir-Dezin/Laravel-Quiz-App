<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateQuestionRequest;
use Illuminate\Http\Request;
use App\Services\QuestionService;
use Illuminate\Support\Facades\Gate;

class QuestionController extends Controller
{
    protected $questionService;

    public function __construct(QuestionService $questionService)
    {
        $this->questionService = $questionService;
    }

    public function createQuestion(CreateQuestionRequest $request)
    {
        // Check permission
        Gate::authorize('User can assign quizzes to students');

       

        $question = $this->questionService->createQuestion($request->all());

        return response()->json(['message' => 'Question created successfully', 'question' => $question], 201);
    }
}
