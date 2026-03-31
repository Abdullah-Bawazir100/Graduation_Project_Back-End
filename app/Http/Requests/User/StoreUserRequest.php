<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rule;
use App\Domain\User\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use DateTime;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'firstName'    => ['required', 'string', 'max:255'],
            'lastName'     => ['required', 'string', 'max:255'],
            'dateOfBirth' => ['required', 'date', 'before:today'],
            'idCard'       => ['required', 'file', 'mimes:pdf'],
            'phone'         => ['required', 'string', 'max:20'],
            'departmentID' => ['required', 'integer', 'exists:departments,id'],
        ];
    }

    public function messages(): array
    {
        return [
            //'idCard.mimes' => 'The ID Card must be a PDF file.',
            //'password.confirmed' => 'Password confirmation does not match.',
        ];
    }

}
