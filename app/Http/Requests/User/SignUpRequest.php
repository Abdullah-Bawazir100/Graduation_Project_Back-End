<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class SignUpRequest extends FormRequest
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
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'departmentID' => 'required|integer|exists:departments,id',
        ];
    }

    public function messages(): array
    {
        return [
            'firstName.required' => 'First name is required.',
            'firstName.string' => 'First name must be a string.',
            'firstName.max' => 'First name cannot exceed 255 characters.',
            'lastName.required' => 'Last name is required.',
            'lastName.string' => 'Last name must be a string.',
            'lastName.max' => 'Last name cannot exceed 255 characters.',
            'departmentID.required' => 'Department ID is required.',
            'departmentID.integer' => 'Department ID must be an integer.',
            'departmentID.exists' => 'The specified department does not exist.',
        ];
    }
}
