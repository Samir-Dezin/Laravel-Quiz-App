<?php

namespace App\Services;

use App\Models\QuizAssignment;
use Illuminate\Support\Facades\DB;

class QuizAssignmentService
{
    public function assignQuizToStudents($quizId, $studentIds)
    {
        DB::beginTransaction();
        try {
            foreach ($studentIds as $studentId) {
                QuizAssignment::create([
                    'quiz_id' => $quizId,
                    'student_id' => $studentId,
                    'assigned_at' => now(),
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e; // Rethrow exception for handling in the controller
        }
    }
}
