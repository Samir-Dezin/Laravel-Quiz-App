<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\QuizAttemptController;
use App\Http\Controllers\StudentSubmissionController;

// API routes
    Route::group(['middleware' => 'api'], function ()
    {
    // Auth routes
    Route::post('/admin/register', [AuthController::class, 'register']); // if needed
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    // To get list of all office memebers
    Route::middleware('auth:api')->get('/users/managers-supervisors', [UserController::class, 'listManagersAndSupervisors']);
    
    // Password setup routes
    Route::put('/password/setup', [PasswordController::class, 'updatePassword'])->name('password.setup');

    // Student submission routes
    Route::post('/submissions', [StudentSubmissionController::class, 'submit']);
    Route::post('/submissions/{id}/accept', [StudentSubmissionController::class, 'accept']);
    Route::post('/submissions/{id}/reject', [StudentSubmissionController::class, 'reject']);
    Route::get('/students', [StudentSubmissionController::class, 'showAllStudents']); 
    // To sow accepted students
    Route::get('/students/accepted', [UserController::class, 'listStudents'])->name('students.list');

    // Quiz routes

    // Create a quiz
    Route::post('/quizzes', [QuizController::class, 'create']);

    // Get all quizzes
    Route::get('/all-quizzes', [QuizController::class, 'index']);
    
    // Assign quiz to a user
    Route::post('/quizzes/assign', [QuizController::class, 'assignQuizToUser']);
    Route::get('/quizzes/assigned', [QuizController::class, 'listQuizzesAssigned']);
    Route::get('/student/quizzes', [QuizController::class, 'showAssignedQuizzes']);
     Route::get('/student/pending-quizzes', [QuizController::class, 'showPendingAssignedQuizzes']);

    // To show quiz to user to be attempted
    Route::get('/quizzes/{id}/attempt', [QuizController::class, 'getQuizQuestionsById']);

    // Attempt a quiz
    Route::post('/quizzes/{quiz}/submit', [QuizAttemptController::class, 'submitQuiz']);

    // Get quiz results
    Route::get('/admin/{quiz}/results', [QuizAttemptController::class, 'getResults']);
    Route::middleware('auth:api')->get('/students/{quizId}/results', [QuizController::class, 'showStudentQuizResult']);
    Route::middleware('auth:api')->get('/student/quiz-results', [QuizController::class, 'getAllStudentQuizResults']);
   
    });

    

