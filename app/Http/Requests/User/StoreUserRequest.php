<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rule;
use App\Domain\User\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use App\Application\User\DTOs\UserDTO;
use Illuminate\Support\Facades\Auth;
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
            'first_name'    => ['required', 'string', 'max:255'],
            'last_name'     => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            //'id_card'       => ['required', 'file', 'mimes:pdf', 'max:2048'],
            'phone'         => ['required', 'string', 'max:20'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
            'role'          => ['required', Rule::in(array_map(fn($r) => $r->value, UserRole::cases()))],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_card.mimes' => 'The ID Card must be a PDF file.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }

    public function toDTO(): UserDTO
    {
        return new UserDTO(
            id: null,
            firstName: $this->input('first_name'),
            lastName: $this->input('last_name'),
            dateOfBirth: new DateTime($this->input('date_of_birth')),
            idCard: null,
            userName: null,
            phone: $this->input('phone'),
            createdBy: $this->input('created_by') ?? Auth::id(),
            departmentID: $this->input('department_id'),
            role: $this->input('role'),
            mustChangePassword: true
        );
    }
}