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
            'firstName'    => ['sometimes', 'string', 'max:255'],
            'lastName'     => ['sometimes', 'string', 'max:255'],
            'dateOfBirth' => ['sometimes', 'date', 'before:today'],
            'idCard'       => ['sometimes', 'file', 'mimes:pdf'],
            'phone'         => ['sometimes', 'string', 'max:20'],
            'role'          => ['sometimes', Rule::in(array_map(fn($r) => $r->value, UserRole::cases()))],
            'departmentID' => ['sometimes', 'integer', 'exists:departments,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'firstName.string' => 'الأسم الأول يجب أن يكون نص.',
            'firstName.max'    => 'الأسم الأول يجب ألا يتجاوز 255 حرفًا.',

            'lastName.string' => 'الأسم الأخير يجب أن يكون نص.',
            'lastName.max'    => 'الأسم الأخير يجب ألا يتجاوز 255 حرفًا.',

            'dateOfBirth.date'   => 'تاريخ الميلاد يجب أن يكون تاريخًا صحيحًا.',
            'dateOfBirth.before' => 'تاريخ الميلاد يجب أن يكون قبل اليوم.',

            'idCard.file'  => 'يجب أن يكون الملف المرفوع صحيحًا.',
            'idCard.mimes' => 'يجب أن يكون الملف بصيغة PDF فقط.',

            'phone.string' => 'رقم الهاتف يجب أن يكون نصًا.',
            'phone.max'    => 'رقم الهاتف يجب ألا يتجاوز 20 رقمًا.',

            'role.in' => 'الدور المحدد غير صالح.',

            'departmentID.integer' => 'القسم يجب أن يكون رقمًا صحيحًا.',
            'departmentID.exists'  => 'القسم المحدد غير موجود.',
        ];
    }

}
