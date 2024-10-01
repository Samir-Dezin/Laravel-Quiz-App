<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentSubmission;
use App\Http\Controllers\Controller;
use App\Services\StudentSubmissionService;
use App\Http\Requests\StudentSubmissionRequest; // Create this request for validation

class StudentSubmissionController extends Controller
{
    protected $submissionService;

    public function __construct(StudentSubmissionService $submissionService)
    {
        $this->submissionService = $submissionService;
    }

    // Submit a new student submission
    public function submit(StudentSubmissionRequest $request)
    {
        return $this->submissionService->submit($request);
    }

    // Accept a student submission
    public function accept($id)
    {
        return $this->submissionService->acceptSubmission($id);
    }
    

    // Reject a student submission
    public function reject($id)
    {
    // Call the rejectSubmission method in the service
    return $this->submissionService->rejectSubmission($id);
    }


    // Optional: List all submissions
   public function showAllStudents()
    {
        // Fetch all submissions without timestamps
        $submissions = StudentSubmission::select('id', 'name', 'email', 'phone', 'cv_path', 'status', 'accepted_at')
            ->get();
            $message='Student submissions retrieved successfully';

        return result($message,200,$submissions);
            
    }
}
