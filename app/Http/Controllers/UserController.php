<?php
namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    // Method to get the list of users with 'student' role
    public function listStudents(Request $request)
    {
        $students = $this->userService->getStudents();
        
        // Optionally transform the response to hide sensitive fields (e.g., password)
        $students = $students->makeHidden(['password', 'remember_token']); 
        
        return response()->json($students, 200);
    }
    // Controller method to list users with manager or supervisor role
    public function listManagersAndSupervisors()
    {
        // Call the service method to fetch users
        $users = $this->userService->getManagersAndSupervisors();

        // Return the list of users in a JSON response
        return response()->json($users, 200);
    }
}
