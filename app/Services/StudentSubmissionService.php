<?php

namespace App\Services;

use App\Models\User;
use App\Mail\RejectionMail;
use Illuminate\Support\Str;
use App\Mail\AcceptanceMail;
use App\Models\StudentSubmission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StudentSubmissionService
{
    // Submitting a student submission
    public function submit($request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:student_submissions,email',
            'phone' => 'nullable|string|max:15',
            'cv' => 'required|file|mimes:pdf,doc,docx|max:2048', // Validate CV file format and size
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return result('Validation error', 422, $validator->errors());
        }

        // Check if the file exists in the request
        if (!$request->hasFile('cv')) {
            return result('CV file is missing', 400);
        }

        try {
            // Store the CV file in the 'cvs' directory
            $cvPath = $request->file('cv')->store('cvs');

            // Create the student submission with validated data
            $submission = StudentSubmission::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone ?? null,
                'cv_path' => $cvPath,
                'status' => 'pending',
            ]);

            return result('Submission successful', 201, $submission);

        } catch (\Exception $e) {
            return result('Error while submitting', 500, $e->getMessage());
        }
    }

    // Accept the student submission and register the user
     public function acceptSubmission($id)
    {
        $submission = StudentSubmission::findOrFail($id);

        try {
            // Create a new user from the submission details
            $user = User::create([
                'name' => $submission->name,
                'email' => $submission->email,
                'password' => Hash::make('password'), // Temporary password
            ]);

            // Assign the "student" role to the new user
            $user->assignRole('student');

            // Update the submission status to accepted
            $submission->update([
                'status' => 'accepted',
                'accepted_at' => now(),
            ]);

            // Generate a token and store it in the remember_token column
            $token = Str::random(60);
            $user->setRememberToken($token); // Store the token in the remember_token field
            $user->save();

            // Send the password setup email to the student
            $this->sendAcceptanceEmail($user);

            return result('Submission accepted, user registered', 200, $submission);

        } catch (\Exception $e) {
            return result('Error while accepting submission', 500, $e->getMessage());
        }
    }

    // Reject submission
    public function rejectSubmission($id)
    {
        // Find the submission by ID or throw a 404 error
        $submission = StudentSubmission::findOrFail($id);

        try {
            // Update the submission status to "rejected"
            $submission->update([
                'status' => 'rejected',
                'rejected_at' => now(), // Store the time of rejection
            ]);

            // Send the rejection email and ensure it's queued
            $this->sendRejectionEmail($submission);

            return result('Submission rejected successfully', 200, $submission);

        } catch (\Exception $e) {
            return result('Error while rejecting submission', 500, $e->getMessage());
        }
    }

    // ################# MAIL HELPER ######################

    // Send acceptance email for password setup
    private function sendAcceptanceEmail(User $user)
{
    try {
        // Generate a random token and store it in the remember_token
        $token = Str::random(60);
        $user->remember_token = $token;
        $user->save(); // Save the token in the user record

        // Generate password setup link using the random token
        $setupLink = url('api/password/setup?token=' . $token . '&email=' . urlencode($user->email));

        // Queue the acceptance email using the AcceptanceMail class
        Mail::to($user->email)->queue(new AcceptanceMail($user, $setupLink));

    } catch (\Exception $e) {
        Log::error('Mail sending failed: ' . $e->getMessage());
        return result('Error sending acceptance email', 500, $e->getMessage());
    }
}

    // Rejection Mail Method:
    private function sendRejectionEmail(StudentSubmission $submission)
    {
        try {
            $emailData = [
                'subject' => 'Submission Rejected',
                'name' => $submission->name,
            ];

            // Queue the rejection email
            Mail::to($submission->email)->queue(new RejectionMail($emailData));

        } catch (\Exception $e) {
            Log::error('Failed to queue rejection email: ' . $e->getMessage());
        }
    }
}
