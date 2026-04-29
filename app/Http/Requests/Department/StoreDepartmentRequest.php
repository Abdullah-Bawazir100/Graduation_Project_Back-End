<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
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
            'name' => 'required|string|max:255|not_regex:/^\d+$/|unique:departments,name',
        ];
    }

        public function messages(): array
        {
            return [
                'name.required' => 'اسم القسم مطلوب.',
                'name.string' => 'اسم القسم يجب ان يكون نصاً.',
                'name.max' => 'اسم القسم لا يمكن ان يزيد عن 255 حرفا.',
                'name.unique' => 'اسم القسم مسجل مسبقاً.',
                'name.not_regex' => 'لا يمكن أن يكون اسم القسم أرقام فقط.',
            ];
        }
}

