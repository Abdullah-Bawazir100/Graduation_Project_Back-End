<?php

namespace App\Http\Requests\ActivityType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActivityTypeRequest extends FormRequest
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
            'name' => ['sometimes' , 'string' , 'max:255' , 'not_regex:/^\d+$/']
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'اسم نوع النشاط يجب ان يكون نصاً.',
            'name.max' => 'اسم نوع النشاط لا يمكن ان يزيد عن 255 حرفا.',
            'name.not_regex' => 'لا يمكن أن يكون اسم نوع النشاط أرقام فقط.',
        ];
    }
}
