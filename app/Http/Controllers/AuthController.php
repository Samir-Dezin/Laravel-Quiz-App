<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $authService;

    // Inject the AuthService into the controller
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // Register function
    public function register(UserRequest $request)
{
    // Delegate the register logic to the service
    return $this->authService->register($request->validated());
}


    // Login function
    
        public function login(UserRequest $request)
{
    // Attempt to authenticate the user with the provided credentials
    if (!auth()->attempt($request->validated())) {
        // If authentication fails, return an error message with 401 Unauthorized status
        return result('Invalid credentials. Please try again.', 401);
    }

    // If login is successful, retrieve the authenticated user
    $user = auth()->user();

    // Get the user's role (assuming a user can have only one role)
    $role = $user->getRoleNames()->first();

    // Get the user's permissions as an array of permission names
    $permissions = $user->getAllPermissions()->pluck('name')->toArray();

    // Delegate further logic to the auth service (e.g., JWT token generation)
    $response = $this->authService->login($request->validated());

    // Prepare the response data
    $data = array_merge([
        "name" => $user->name,
        "email" => $user->email,
        "role" => $role,
        "permissions" => $permissions
    ], $response);

    // Return the success response
    return result('You have logged in successfully', 200, $data);
}

    

    // Logout function
    public function logout()
    {
        // Delegate the logout logic to the service
        $response = $this->authService->logout();
        $message="You have been logged out successfully";

        return result($message,200,$response);
    }
}
