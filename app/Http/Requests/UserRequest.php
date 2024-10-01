<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        switch ($this->route()->getActionMethod()) {
            case 'register':
                return [
                    'name' => 'required|string|min:2',
                    'email' => 'required|email|min:2|unique:users',
                    'password' => 'string|min:8',
                    'role' => 'required|string|in:manager,supervisor',
                ];
            case 'login':
                return [
                    'email' => 'required|email|min:2',
                    'password' => 'required|string|min:8',
                ];
            case 'logout':
                return [];
            default:
                return [];
        }
    }

    public function messages()
    {
        return [
            // General messages
            'required' => ':attribute is required',
            'string' => ':attribute must be a string',
            'email' => ':attribute must be a valid email address',
            'min' => ':attribute must be at least :min characters long',
            // 'confirmed' => 'Password confirmation does not match',

            // Specific attribute messages
            'name.required' => 'Name is required for registration',
            'email.required' => 'Email is required for login and registration',
            'email.unique' => 'The email has already been taken, please choose another one',
            'password.required' => 'Password is required',
            'role.required' => 'Role is required for registration',
            'role.in' => 'The role must be either manager or student',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Name',
            'email' => 'Email Address',
            'password' => 'Password',
            'role' => 'User Role',
        ];
    }
}
