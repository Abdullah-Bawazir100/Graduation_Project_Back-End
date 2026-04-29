<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'userName' => 'required|string',
            'phone' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'userName.required' => 'اسم المستخدم مطلوب.',
            'userName.string' => 'يجب أن يكون اسم المستخدم نصًا.',

            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.string' => 'يجب أن يكون رقم الهاتف نصًا.',
            
            'new_password.required' => 'كلمة المرور الجديدة مطلوبة.',
            'new_password.string' => 'كلمة المرور الجديدة يجب أن تكون نصًا.',
            'new_password.min' => 'كلمة المرور الجديدة يجب أن تكون على الأقل 8 أحرف.',
            'new_password.confirmed' => 'تأكيد كلمة المرور الجديدة غير متطابق.',
        ];
    }
}
