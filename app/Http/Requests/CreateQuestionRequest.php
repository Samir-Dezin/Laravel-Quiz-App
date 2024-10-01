<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuestionRequest extends FormRequest
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
            'quiz_id' => 'required|exists:quizzes,id',
            'question' => 'required|string',
            'options' => 'required|array|min:4|max:4', // 4 options
            'correct_answer' => 'required|string',
        ];
    }
}
