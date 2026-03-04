<?php

namespace App\Http\Requests\User;
use Illuminate\Validation\Rule;
use App\Domain\User\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'first_name'    => ['required', 'string', 'max:255'],
            'last_name'     => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            //'id_card'       => ['required', 'file', 'mimes:pdf', 'max:2048'],
            'phone'         => ['required', 'string', 'max:20'],
            'email'         => ['required', 'email', 'unique:users,email'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
            'role'          => ['required', Rule::in(array_map(fn($r) => $r->value, UserRole::cases()))],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'created_by'    => ['nullable', 'integer', 'exists:users,id'],
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
