<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'userName' => 'required|string|max:255',
            'password' => 'required|string',
        ];

    }    
    public function messages(): array
    {
        return [
            'userName.required' => 'The userName field is required.',
            'userName.string' => 'The userName must be a string.',
            'userName.max' => 'The userName may not be greater than 255 characters.',
            'password.required' => 'The password field is required.',
            'password.string' => 'The password must be a string.',
        ];
    }
}
