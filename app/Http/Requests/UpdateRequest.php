<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Authorize the request for all users
    }

    public function rules()
    {
        return [
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'token.required' => 'The token is required to verify your identity.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'The email address must be a valid email format.',
            'password.required' => 'Please enter a password.',
            'password.string' => 'The password must be a string.',
            'password.min' => 'The password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
