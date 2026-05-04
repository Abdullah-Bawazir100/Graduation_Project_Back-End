<?php

namespace App\Http\Requests\CheritableCompany;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCharitableCompanyRequest extends FormRequest
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
            'byLawsCopy' => [
                'sometimes',
                'file',
                'mimes:jpeg,png,jpg,pdf',
                'max:10240',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'byLawsCopy.file' => 'الصورة من النظام الأساسي يجب أن تكون ملفا صالحا.',
            'byLawsCopy.mimes' => 'الصورة من النظام الأساسي يجب ان تكون من نوع jpeg,png,jpg,pdf.',
            'byLawsCopy.max' => 'الصورة من النظام الأساسي يجب الا تتجاوز 10MB.',
        ];
    }
}
