<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rule;
use App\Domain\User\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use App\Application\User\DTOs\UserDTO;
use Illuminate\Support\Facades\Auth;
use DateTime;

class UpdateUserRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        return [
            'first_name'    => ['sometimes', 'string', 'max:255'],
            'last_name'     => ['sometimes', 'string', 'max:255'],
            'date_of_birth' => ['sometimes', 'date', 'before:today'],
            'id_card'       => ['sometimes', 'file', 'mimes:pdf', 'max:2048'],
            'phone'         => ['sometimes', 'string', 'max:20'],
            'password'      => ['nullable', 'string', 'min:8', 'confirmed'],
            'role'          => ['sometimes', Rule::in(array_map(fn($r) => $r->value, UserRole::cases()))],
            'department_id' => ['sometimes', 'integer', 'exists:departments,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_card.mimes' => 'The ID Card must be a PDF file.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }

}
