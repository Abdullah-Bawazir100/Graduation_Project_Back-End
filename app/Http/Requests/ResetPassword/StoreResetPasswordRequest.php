<?php

namespace App\Http\Requests\ResetPassword;

use Illuminate\Foundation\Http\FormRequest;

class StoreResetPasswordRequest extends FormRequest
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
            'userName' => ['required' , 'string' , 'exists:app_users,user_name']
        ];
    }

    public function messages()
    {
        return [
            'userName.required' => 'اسم المستخدم مطلوب.',
            'userName.string' => 'الاسم يجب ان يكون نص.',
            'userName.exists' => 'اسم المستخدم غير موجود.'
        ];
    }
}
