<?php

namespace App\Http\Requests\ResetPassword;

use Illuminate\Foundation\Http\FormRequest;

class VerifyCodeRequest extends FormRequest
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
            'userId' => ['required', 'integer' , 'exists:app_users,id'],
            'code' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'userId.required' => 'المستخدم مطلوب.',
            'userId.integer' => 'المستخدم يجب ان يكون رقم.',
            'userId.exists' => 'المستخدم غير موجود.',

            'code.required' => 'كود التحقق مطلوب.',
            'code.string' => 'كود التحقق يجب ان يكون نص.',
        ];
    }
}
