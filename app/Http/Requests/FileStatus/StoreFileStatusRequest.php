<?php

namespace App\Http\Requests\FileStatus;

use Illuminate\Foundation\Http\FormRequest;

class StoreFileStatusRequest extends FormRequest
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
            'statusName' => ['required' , 'string' , 'unique:file_status,status_name' , 'not_regex:/^\d+$/'],
            'statusDescription' => ['nullable' , 'string' , 'not_regex:/^\d+$/'],
        ];
    }

    public function messages()
    {
        return [
            'statusName.required' => 'اسم حالة الملف مطلوب.',
            'statusName.string' => 'اسم حالة الملف يجب ان يكون نصاً.',
            'statusName.unique' => 'اسم حالة الملف موجود بالفعل.',
            'statusName.not_regex' => 'لا يمكن أن يكون اسم حالة الملف أرقام فقط.',

            'statusDescription.string' => 'وصف حالة الملف يجب ان يكون نصاً.',
            'statusDescription.not_regex' => 'لا يمكن أن يكون وصف حالة الملف أرقام فقط.'
        ];
    }
}

