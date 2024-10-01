<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Models\User;
use App\Services\PasswordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    protected $passwordService;

    // Inject the PasswordService into the controller
    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    // Update password function
    public function updatePassword(UpdatePasswordRequest $request)
    {
        
        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Check if the user exists
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Check if the provided token matches the remember_token
        if ($user->remember_token !== $request->token) {
            return response()->json(['error' => 'Invalid token or email'], 400);
        }

        // Update the user's password
        $user->password = Hash::make($request->password);
        $user->remember_token = null; // Clear the token after use
        $user->save();

        return response()->json(['message' => 'Password updated successfully'], 200);
    }


    //role update password:
    public function showSetupForm(Request $request)
    {
        // Check if the URL is valid
        if (!$request->hasValidSignature()) {
            return response()->json(['error' => 'Invalid or expired link.'], 403);
        }

        // Show the password setup form
        return view('auth.password_setup', [
            'token' => $request->token,
            'email' => $request->email,
        ]);
    }
}
