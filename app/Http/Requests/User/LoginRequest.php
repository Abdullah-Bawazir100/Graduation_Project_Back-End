<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'userName' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ];

    }
    public function messages(): array
    {
        return [
            'userName.required' => 'اسم المستخدم مطلوب.',
            'userName.string'   => 'اسم المستخدم يجب أن يكون نصًا.',
            'userName.max'      => 'اسم المستخدم يجب ألا يزيد عن 255 حرفًا.',

            'password.required' => 'كلمة المرور مطلوبة.',
            'password.string'   => 'كلمة المرور يجب أن تكون نصًا.',
            'password.min'      => 'كلمة المرور يجب أن تكون على الأقل 8 أحرف.',
        ];
    }
}
