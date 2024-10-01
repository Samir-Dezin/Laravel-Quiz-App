<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuizAttemptRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'answers' => 'required|array',
            'answers.*' => 'required|string', // Ensures all answers are provided and in correct format
            'video' => 'required|file|mimes:mp4,mov,avi|max:20480', // Validates video file with a max size of 20MB
        ];
    }

    public function messages()
    {
        return [
            'answers.required' => 'You must provide answers for all questions.',
            'video.required' => 'A video recording is required for the quiz attempt.',
            'video.mimes' => 'The video format must be mp4, mov, or avi.',
            'video.max' => 'The video size must not exceed 20MB.',
        ];
    }
}
