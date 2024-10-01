<?php
namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Carbon\Carbon;

class PasswordService
{
    // Handle password update logic
    public function updatePassword($token, $email, $password)
    {
        // Find the user by email
        $user = User::where('email', $email)->first();
        
        // Check if the user exists
        if (!$user) {
            $message = "User not found";
            return result($message, 404);
        }

        // Check if the provided token matches the remember_token
        if ($user->remember_token !== $token) {
            $message = "Invalid token or email";
            return result($message, 400);
        }

        // Optionally check token expiration (e.g., 24 hours)
        $tokenExpiryHours = 24;
        if (Carbon::parse($user->updated_at)->addHours($tokenExpiryHours)->isPast()) {
            $message = "Token expired";
            return result($message, 400);
        }

        // Update the user's password
        $user->password = Hash::make($password);
        $user->remember_token = null; // Clear the token after use
        $user->save();

        // Log the password reset
        Log::info("Password successfully updated for user: {$user->email}");

        $message = "Password updated successfully";
        return result($message, 200);
    }
}
