<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\User;
use App\Models\Question;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class QuizService
{
    // Create a quiz with questions
    public function createQuiz(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array',
            'questions.*.question' => 'required|string',
            'questions.*.options' => 'required|array|min:4|max:4',
            'questions.*.correct_answer' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $quiz = Quiz::create([
                'title' => $request->title,
                'description' => $request->description,
                'activation_time' => now(),
                'expiration_time' => now()->addHours(24),
            ]);

            foreach ($request->questions as $q) {
                Question::create([
                    'quiz_id' => $quiz->id,
                    'question' => $q['question'],
                    'options' => json_encode($q['options']),
                    'correct_answer' => $q['correct_answer'],
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Quiz created successfully', 'quiz' => $quiz], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error creating quiz: ' . $e->getMessage()], 500);
        }
    }

    // List all quizzes
    public function listQuizzes() {
    $quizzes = Quiz::with('questions')->get();

    // Loop through each quiz and hide timestamps for both quizzes and their questions
    $quizzes->each(function ($quiz) {
        // Remove timestamps from quiz
        $quiz->makeHidden(['created_at', 'updated_at']);
        
        // Remove timestamps from each question
        $quiz->questions->each(function ($question) {
            $question->makeHidden(['created_at', 'updated_at']);
        });
    });

    return response()->json($quizzes);
}



    // Get quiz by ID
    public function getQuizQuestions($id)
{
    // Fetch the quiz by ID and load its questions with pagination
    $quiz = Quiz::findOrFail($id);

    // Fetch questions related to the quiz and paginate them
    $questions = $quiz->questions()->get();

    // Return the paginated questions response
    return result("Quiz retrieved succesfully",200,$questions);
}


    // Assign quiz to a user with a 'student' role
    public function assignQuizToUsers(Request $request)
{
    $request->validate([
        'student_ids' => 'required|array',
        'student_ids.*' => 'exists:users,id', // Validate each user_id
        'quiz_id' => 'required|exists:quizzes,id',
    ]);

    DB::beginTransaction();
    try {
        $quiz = Quiz::findOrFail($request->quiz_id);
        $errorUsers = [];

        // Loop through each user_id in the array
        foreach ($request->student_ids as $user_id) {
            $user = User::findOrFail($user_id);

            // Check if the user has the role of 'student'
            if (!$user->hasRole('student')) {
                $errorUsers[] = $user_id;
                continue; // Skip users without the 'student' role
            }

            // Assign quiz to the user, ensuring no duplicates
            $user->quizzes()->syncWithoutDetaching($quiz->id);
        }

        DB::commit();

        if (count($errorUsers) > 0) {
            return response()->json([
                'message' => 'Quiz assigned, but some users do not have the student role.',
                'error_users' => $errorUsers
            ], 207); // 207 Multi-Status (for partial success)
        }

        return response()->json(['message' => 'Quiz assigned successfully to all users'], 201);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Error assigning quiz: ' . $e->getMessage()], 500);
    }
}
// Method to fetch all quizzes assigned to users
public function getQuizzesAssignedToUsers()
{
    return Quiz::with('users')->get(); 
}

    // Method to fetch all quizzes assigned to student
    public function getQuizzesAssignedToStudent($studentId)
    {
        // Fetch quizzes assigned to the user (student)
        return Quiz::whereHas('users', function($query) use ($studentId) {
            $query->where('users.id', $studentId);
        })->with('users')->get();
    }

    //Method to fetch all pending quizzes to students:
    public function getPendingAssignedQuizzesForStudent()
    {
        // Get the authenticated student ID
        $studentId = Auth::id();

        // Get the quizzes that the student has already attempted
        $attemptedQuizIds = QuizAttempt::where('student_id', $studentId)
            ->pluck('quiz_id') // Get the IDs of the quizzes the student attempted
            ->toArray();

        // Fetch all quizzes assigned to the student via pivot table
        $assignedQuizzes = Quiz::whereHas('users', function ($query) use ($studentId) {
            $query->where('user_id', $studentId); // Only quizzes assigned to this student
        })
        ->whereNotIn('id', $attemptedQuizIds) // Exclude quizzes that the student has already attempted
        ->get();

        return $assignedQuizzes;
    }



    // Attempt a quiz
    public function attemptQuiz(Request $request, $quizId)
{
    // Check if the user is authenticated
    if (!auth()->check()) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // Validate the answers and optional video file
    $request->validate([
        'answers' => 'required|array',
        'answers.*' => 'required|string', // Each answer must be a string
        'video' => 'nullable|file|mimes:mp4,mov,avi|max:10240' // Validate the video if uploaded
    ]);

    // Find the quiz and load its questions
    $quiz = Quiz::with('questions')->findOrFail($quizId);

    // Start a database transaction
    DB::beginTransaction();
    try {
        // If the quiz has no questions, return an error
        if ($quiz->questions->isEmpty()) {
            return response()->json(['error' => 'No questions available for this quiz'], 400);
        }

        // Initialize the score and an array to store the results
        $score = 0;
        $attemptedQuestions = [];

        // Iterate over the questions and match them with the provided answers
        foreach ($quiz->questions as $index => $question) {
            // If the answer exists in the array, process it
            if (isset($request->answers[$index])) {
                $isCorrect = ($question->correct_answer === $request->answers[$index]);
                if ($isCorrect) {
                    $score++;
                }

                // Add to the attempted questions array
                $attemptedQuestions[] = [
                    'question_id' => $question->id,
                    'given_answer' => $request->answers[$index],
                    'is_correct' => $isCorrect,
                ];
            } else {
                return response()->json(['error' => 'Answer missing for question: ' . ($index + 1)], 400);
            }
        }

        // Optionally handle video upload
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('quiz_videos', 'public');
        }

        // Store the quiz attempt
        QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => auth()->id(),
            'attempted_at' => now(),
            'score' => $score,
            'answers' => json_encode($attemptedQuestions),
            'video_path' => $videoPath ?? null // Save video path if uploaded
        ]);

        // Commit the transaction
        DB::commit();

        return response()->json(['message' => 'Quiz attempted successfully', 'score' => $score], 201);
    } catch (\Exception $e) {
        // Rollback the transaction in case of an error
        DB::rollBack();
        return response()->json(['error' => 'Error submitting quiz attempt: ' . $e->getMessage()], 500);
    }
}

    public function getQuizResults($quizId)
{
    // Ensure the authenticated user is an admin
    // if (!auth()->user()->hasRole('admin')) {
    //     return response()->json(['error' => 'Unauthorized. Admin access required.'], 403);
    // }

    // Find the quiz or return 404 if not found
    $quiz = Quiz::with('questions')->findOrFail($quizId);

    // Fetch all quiz attempts for this quiz
    $attempts = QuizAttempt::where('quiz_id', $quizId)->get();

    // Check if any attempts exist
    if ($attempts->isEmpty()) {
        return response()->json([
            'error' => 'No attempts found for this quiz.',
            'total_questions' => $quiz->questions->count(),
            'correct_answers' => 0,
            'score' => '0%',
            'attempts' => [],
        ], 404);
    }

    // Calculate the number of correct answers across all attempts
    $correctAnswers = $attempts->sum(function ($attempt) {
        // Decode the answers stored in the attempt
        $answers = json_decode($attempt->answers, true);

        // If answers are not decoded correctly, return 0
        if (!is_array($answers)) {
            return 0;
        }

        // Sum the correct answers in this attempt
        return collect($answers)
            ->where('is_correct', true)
            ->count();
    });

    // Get the total number of questions in the quiz
    $totalQuestions = $quiz->questions->count();

    // Calculate the score as a percentage
    $score = ($totalQuestions > 0) ? ($correctAnswers / $totalQuestions) * 100 : 0;

    // Return the quiz results in the response
    return response()->json([
        'total_questions' => $totalQuestions,
        'correct_answers' => $correctAnswers,
        'score' => number_format($score, 2) . '%',
        'attempts' => $attempts,
    ]);
}

    public function getAllStudentQuizResults()
    {
        $user = Auth::user(); // Get the authenticated student

        // Fetch all quiz attempts by the student
        $attempts = QuizAttempt::with('quiz')->where('student_id', $user->id)->get();

        if ($attempts->isEmpty()) {
            return null; // No attempts found
        }

        // Initialize an array to hold all attempts results
        $results = [];

        // Iterate over each attempt to calculate results
        foreach ($attempts as $attempt) {
            $quiz = $attempt->quiz; // Get the associated quiz
            $answers = json_decode($attempt->answers, true); // Decode the stored answers
            $correctAnswers = collect($answers)->where('is_correct', true)->count();
            $totalQuestions = $quiz->questions->count();
            $score = ($totalQuestions > 0) ? ($correctAnswers / $totalQuestions) * 100 : 0;

            // Store the result for this attempt
            $results[] = [
                'quiz_title' => $quiz->title,
                'attempted_at' => $attempt->attempted_at,
                'score' => number_format($score, 2) . '%',
                'correct_answers' => $correctAnswers,
                'total_questions' => $totalQuestions,
                'answers' => $answers,
            ];
        }

        return $results; // Return the results array
    }
}
