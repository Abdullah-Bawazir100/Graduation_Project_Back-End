<?php

namespace App\Http\Requests\JobType;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobTypeRequest extends FormRequest
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
            'name' => 'required|string|max:255|not_regex:/^\d+$/|unique:job_types,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم نوع الوظيفة مطلوب.',
            'name.string' => 'اسم نوع الوظيفة يجب ان يكون نصاً.',
            'name.max' => 'اسم نوع الوظيفة لا يمكن ان يزيد عن 255 حرفا.',
            'name.unique' => 'اسم نوع الوظيفة مسجل مسبقاً.',
            'name.not_regex' => 'لا يمكن أن يكون اسم نوع الوظيفة أرقام فقط.',
        ];
    }
}
