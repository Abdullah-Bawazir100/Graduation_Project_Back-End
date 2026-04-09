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
            'image' => ['required' , 'image' , 'mimes:png,jpg,jpeg,gif' , 'max:2048'],
            'role' => ['required' , Rule::in(array_map(fn($r) => $r->value, UserRole::cases()))],
            'departmentID' => ['required', 'integer', 'exists:departments,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'firstName.required' => 'الأسم الأول مطلوب.',
            'firstName.string'   => 'الأسم الأول يجب أن يكون نص.',
            'firstName.max'      => 'الأسم الأول يجب ألا يتجاوز 255 حرفًا.',

            'lastName.required' => 'الأسم الأخير مطلوب.',
            'lastName.string'   => 'الأسم الأخير يجب أن يكون نص.',
            'lastName.max'      => 'الأسم الأخير يجب ألا يتجاوز 255 حرفًا.',

            'dateOfBirth.required' => 'تاريخ الميلاد مطلوب.',
            'dateOfBirth.date'     => 'تاريخ الميلاد يجب أن يكون تاريخًا صحيحًا.',
            'dateOfBirth.before'   => 'تاريخ الميلاد يجب أن يكون قبل اليوم.',

            'idCard.required' => 'ملف البطاقة الشخصية مطلوب.',
            'idCard.file'     => 'يجب أن يكون الملف المرفوع صحيحًا.',
            'idCard.mimes'    => 'يجب أن يكون الملف بصيغة PDF فقط.',

            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.string'   => 'رقم الهاتف يجب أن يكون نصًا.',
            'phone.max'      => 'رقم الهاتف يجب ألا يتجاوز 20 رقمًا.',

            'image.required' => 'صورة الشخص مطلوبة.',
            'image.max' => 'حجم الصورة يجب ألا يتجاوز 2 MB .',

            'role.required' => 'الدور مطلوب.',
            'role.in' => 'الدور المحدد غير صالح.',

            'departmentID.required' => 'القسم مطلوب.',
            'departmentID.integer'  => 'القسم يجب أن يكون رقمًا صحيحًا.',
            'departmentID.exists'   => 'القسم المحدد غير موجود.',
        ];
    }

}
