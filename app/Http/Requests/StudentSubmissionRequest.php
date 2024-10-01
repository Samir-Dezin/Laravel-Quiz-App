<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentSubmissionRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Set to true or implement your own authorization logic
    }

    public function rules()
    {
        return [
            'cv' => 'required|file|mimes:pdf,doc,docx|max:2048', // Validate the CV file
            'phone' => 'nullable|string|max:15', // Validate the phone if provided
        ];
    }
}
