<?php

namespace App\Http\Controllers;

use App\Services\QuizService;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    protected $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    public function create(Request $request)
    {
        return $this->quizService->createQuiz($request);
    }

    public function index()
    {
        return $this->quizService->listQuizzes();
    }

    public function getQuizQuestionsById(Request $request, $id)
{

    return $this->quizService->getQuizQuestions($id);
}


    public function assignQuizToUser(Request $request){
        return $this->quizService->assignQuizToUsers($request);
    }
    
  public function listQuizzesAssigned(Request $request)
{
    try {
        $quizzes = $this->quizService->getQuizzesAssignedToUsers();

        // Transform the response to hide sensitive fields
        foreach ($quizzes as $quiz) {
            // Check if the 'users' relationship is loaded
            if ($quiz->relationLoaded('users')) {
                // If users are loaded, hide sensitive fields
                $quiz->users = $quiz->users->makeHidden(['password', 'remember_token']);
            } else {
                // If users are not loaded, set it to an empty array
                $quiz->users = [];
            }
        }

        return response()->json($quizzes, 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to retrieve quizzes: ' . $e->getMessage()], 500);
    }
}

// Method to show quizzes assigned to the authenticated student
    public function showAssignedQuizzes()
    {
        // Get the authenticated user's ID (assuming the student is authenticated)
        $studentId = auth()->id();

        // Fetch quizzes assigned to the student
        $quizzes = $this->quizService->getQuizzesAssignedToStudent($studentId);

        // Hide sensitive information about users (if necessary)
        foreach ($quizzes as $quiz) {
            $quiz->users->makeHidden(['password', 'remember_token']);
        }
        $message="Your assigned quizzes!";
        return result($message,200,$quizzes);
    }

    // Method to show pending quizzes for the authenticated student
    public function showPendingAssignedQuizzes()
    {
        // Fetch assigned but unattempted quizzes from the service
        $pendingQuizzes = $this->quizService->getPendingAssignedQuizzesForStudent();

        // Return a message if no pending quizzes are found
        if ($pendingQuizzes->isEmpty()) {
            return result('No pending quizzes found.',200);
        }

        return result("Here are all pending quizzes!",200,$pendingQuizzes);
    }

    // Method to show quizzes result to the authenticated student
    public function getAllStudentQuizResults()
    {
        $results = $this->quizService->getAllStudentQuizResults();

        if (is_null($results)) {
            return response()->json(['message' => 'No attempts found for this student'], 404);
        }

        return response()->json(['message' => 'Quiz results fetched successfully', 'data' => $results], 200);
    }

}
