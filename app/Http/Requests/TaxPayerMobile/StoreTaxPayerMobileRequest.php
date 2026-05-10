<?php

namespace App\Http\Requests\TaxPayerMobile;

use App\Domain\User\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaxPayerMobileRequest extends FormRequest
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
            'firstName' => ['required', 'string', 'max:255' , 'not_regex:/^\d+$/'],
            'lastName'  => ['required', 'string', 'max:255' , 'not_regex:/^\d+$/'],
            'idCard'    => ['required', 'image', 'mimes:png,jpg,jpeg,gif' , 'max:5012'],
            'phone'     => ['required', 'string', 'min:9' , 'unique:app_users,phone'],
            'userName'  => ['required', 'string', 'min:3' , 'unique:app_users,user_name'],
            'password'  => ['required', 'string', 'min:8' , 'confirmed'],
            'image'     => ['required' , 'image' , 'mimes:png,jpg,jpeg,gif' , 'max:5012'],
            'role'      => [Rule::in(array_map(fn($r) => $r->value, UserRole::cases()))],
        ];
    }

    public function messages(): array
    {
        return [
            'firstName.required' => 'الأسم الأول مطلوب.',
            'firstName.string'   => 'الأسم الأول يجب أن يكون نص.',
            'firstName.max'      => 'الأسم الأول يجب ألا يتجاوز 255 حرفًا.',
            'firstName.not_regex' => 'لا يمكن أن يكون الأسم الأول أرقام فقط.',

            'lastName.required' => 'الأسم الأخير مطلوب.',
            'lastName.string'   => 'الأسم الأخير يجب أن يكون نص.',
            'lastName.max'      => 'الأسم الأخير يجب ألا يتجاوز 255 حرفًا.',
            'lastName.not_regex' => 'لا يمكن أن يكون الأسم الأخير أرقام فقط.',

            'idCard.required' => 'ملف البطاقة الشخصية مطلوب.',
            'idCard.file'     => 'يجب أن يكون الملف المرفوع صحيحًا.',
            'idCard.mimes'    => 'يجب أن يكون الملف بصيغة PDF فقط.',
            'idCard.max'     => 'يجب أن لا يتجاوز حجم الملف 5 MB.',

            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.string'   => 'رقم الهاتف يجب أن يكون نصًا.',
            'phone.min'      => 'رقم الهاتف يجب على الأقل أن يكون 9 أرقام.',
            'phone.unique'   => 'رقم الهاتف موجود بالفعل.',

            'userName.required' => 'اسم المستخدم مطلوب.',
            'userName.string'   => 'اسم المستخدم يجب أن يكون نصًا.',
            'userName.min'      => 'اسم المستخدم يجب ألا يقل عن 3 أحرف.',
            'userName.unique'   => 'اسم المستخدم مستخدم بالفعل.',

            'password.required'  => 'كلمة المرور مطلوبة.',
            'password.string'    => 'كلمة المرور يجب أن تكون نصًا.',
            'password.min'       => 'كلمة المرور يجب ألا تقل عن 8 أحرف.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',

            'image.required' => 'صورة الشخص مطلوبة.',
            'image.max' => 'حجم الصورة يجب ألا يتجاوز 5 MB .',

        ];
    }

}
