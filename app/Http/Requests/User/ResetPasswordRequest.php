<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'new_password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'new_password.required'  => 'كلمة المرور الجديدة مطلوبة.',
            'new_password.string'    => 'كلمة المرور الجديدة يجب أن تكون نصًا.',
            'new_password.min'       => 'كلمة المرور الجديدة يجب أن تكون على الأقل 8 أحرف.',
            'new_password.confirmed' => 'تأكيد كلمة المرور الجديدة غير متطابق.',
        ];
    }
}
