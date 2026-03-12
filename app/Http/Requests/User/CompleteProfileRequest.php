<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CompleteProfileRequest extends FormRequest
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
            'dateOfBirth' => 'required|date',
            'idCard' => 'required|file|mimes:pdf|max:2048',
            'phone' => 'required|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'dateOfBirth.required' => 'Date of birth is required.',
            'dateOfBirth.date' => 'Date of birth must be a valid date.',
            'idCard.required' => 'ID card is required.',
            'idCard.file' => 'ID card must be a file.',
            'idCard.mimes' => 'ID card must be a PDF file.',
            'idCard.max' => 'ID card must not exceed 2MB in size.',
            'phone.required' => 'Phone number is required.',
            'phone.string' => 'Phone number must be a string.',
            'phone.max' => 'Phone number must not exceed 20 characters.',
        ];
    }
}
