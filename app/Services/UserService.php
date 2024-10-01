<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getStudents()
    {
        return User::role('student')->get(); // Fetch users with the 'student' role
    }
    // Get Manager and Supervisor
     public function getManagersAndSupervisors()
    {
        // Fetch users with roles and transform to only return role name
        return User::role(['manager', 'supervisor'])
                    ->with('roles') // Eager load roles
                    ->get()
                    ->map(function ($user) {

                        
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $user->roles->pluck('name')->first(), // Get only the first role name
                        ];
                    });
    }
}