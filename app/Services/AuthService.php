<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Support\Str;

use App\Mail\PasswordSetupMail;


class AuthService
{

public function register(array $data)
{
    // Validate the role as either manager or supervisor
    if (!in_array($data['role'], ['manager', 'supervisor'])) {
        return result('Invalid role. User must be either a manager or supervisor.', 400);
    }

    // Start a database transaction
    DB::beginTransaction();

    try {
        // Create the user with a random password
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make(Str::random(10)), // Randomized initial password
        ]);

        // Assign the role
        $user->assignRole($data['role']);

        // Create a token for the remember token
        $token = Str::random(60);

        // Store the token in the remember_token column
        $user->remember_token = Hash::make($token);
        $user->save();

        // Send email with the token to the user
        Mail::to($user->email)->queue(new PasswordSetupMail($user, $token));

        // Commit the transaction
        DB::commit();

        $message = "User registered successfully, check your email to set up your password";

        // Return success response using result helper
        return result($message, 200, $user);

    } catch (\Exception $e) {
        // Rollback the transaction in case of an error
        DB::rollBack();

        // Return error response using result helper
        return result('User registration failed: ' . $e->getMessage(), 500);
    }
}

    // ############### Login Methode #################
    public function login(array $credentials)
    {
        $token = auth()->attempt($credentials);

        if (!$token) {
            return ['error' => 'Unauthorized', 'status' => 401];
        }

        return respondWithToken($token);
    }

    // ############### Logout Methode #################
    public function logout()
    {
        auth()->logout();
        return ['message' => 'User successfully logged out!'];
    }

    
}
