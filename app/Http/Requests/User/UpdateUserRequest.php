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
            'idCard'       => ['sometimes', 'file', 'mimes:pdf'],
            'phone'         => ['sometimes', 'digits:9', 'regex:/^7[0-9]{8}$/', 'unique:app_users,phone'],
            'image'         => ['sometimes', 'image', 'mimes:png,jpg,jpeg,gif' , 'max:5012'],
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

            'idCard.file'  => 'يجب أن يكون الملف المرفوع صحيحًا.',
            'idCard.mimes' => 'يجب أن يكون الملف بصيغة PDF فقط.',

            'phone.digits'   => 'رقم الهاتف يجب أن يتكون من 9 أرقام فقط.',
            'phone.regex'    => 'رقم الهاتف يجب أن يبدأ بالرقم 7 ويتكون من 9 أرقام.',
            'phone.unique' => 'هذا الرقم موجود بالفعل.',

            'image.max' => 'حجم الصورة يجب ألا يتجاوز 5 MB .',

            'role.in' => 'الدور المحدد غير صالح.',

            'departmentID.integer' => 'القسم يجب أن يكون رقمًا صحيحًا.',
            'departmentID.exists'  => 'القسم المحدد غير موجود.',
        ];
    }

}
