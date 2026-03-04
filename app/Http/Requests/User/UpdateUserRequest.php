<?php

namespace App\Http\Requests\User;
use Illuminate\Validation\Rule;
use App\Domain\User\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('id'); // id من route

        return [
            'first_name'    => ['sometimes', 'string', 'max:255'],
            'last_name'     => ['sometimes', 'string', 'max:255'],
            'date_of_birth' => ['sometimes', 'date', 'before:today'],
            'id_card'       => ['sometimes', 'file', 'mimes:pdf', 'max:2048'],
            'user_name'     => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('users', 'user_name')->ignore($userId)
            ],
            'phone'         => ['sometimes', 'string', 'max:20'],
            'email'         => [
                'sometimes',
                'email',
                Rule::unique('users', 'email')->ignore($userId)
            ],
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
