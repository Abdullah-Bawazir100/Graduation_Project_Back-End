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
            'firstName'    => ['sometimes', 'string', 'max:255' , 'not_regex:/^\d+$/'],
            'lastName'     => ['sometimes', 'string', 'max:255' , 'not_regex:/^\d+$/'],
            'dateOfBirth' => ['sometimes', 'date', 'before:today'],
            'idCard'       => ['sometimes', 'file', 'mimes:pdf'],
            'phone'         => ['sometimes', 'string', 'max:20'],
            'image'         => ['sometimes', 'string', 'mimes:png,jpg,jpeg,gif' , 'max:2048'],
            'role'          => ['sometimes', Rule::in(array_map(fn($r) => $r->value, UserRole::cases()))],
            'departmentID' => ['sometimes', 'integer', 'exists:departments,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'firstName.string' => 'الأسم الأول يجب أن يكون نص.',
            'firstName.max'    => 'الأسم الأول يجب ألا يتجاوز 255 حرفًا.',
            'firstName.not_regex' => 'لا يمكن أن يكون الأسم الأول أرقام فقط.',

            'lastName.string' => 'الأسم الأخير يجب أن يكون نص.',
            'lastName.max'    => 'الأسم الأخير يجب ألا يتجاوز 255 حرفًا.',
            'lastName.not_regex' => 'لا يمكن أن يكون الأسم الأخير أرقام فقط.',

            'dateOfBirth.date'   => 'تاريخ الميلاد يجب أن يكون تاريخًا صحيحًا.',
            'dateOfBirth.before' => 'تاريخ الميلاد يجب أن يكون قبل اليوم.',

            'idCard.file'  => 'يجب أن يكون الملف المرفوع صحيحًا.',
            'idCard.mimes' => 'يجب أن يكون الملف بصيغة PDF فقط.',

            'phone.string' => 'رقم الهاتف يجب أن يكون نصًا.',
            'phone.max'    => 'رقم الهاتف يجب ألا يتجاوز 20 رقمًا.',

            'image.max' => 'حجم الصورة يجب ألا يتجاوز 2 MB .',

            'role.in' => 'الدور المحدد غير صالح.',

            'departmentID.integer' => 'القسم يجب أن يكون رقمًا صحيحًا.',
            'departmentID.exists'  => 'القسم المحدد غير موجود.',
        ];
    }

}
