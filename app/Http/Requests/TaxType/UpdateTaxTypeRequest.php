<?php

namespace App\Http\Requests\TaxType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaxTypeRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255|not_regex:/^\d+$/|unique:tax_types,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'اسم نوع الضريبة يجب ان يكون نصاً.',
            'name.max' => 'اسم نوع الضريبة لا يمكن ان يزيد عن 255 حرفا.',
            'name.unique' => 'اسم نوع الضريبة مسجل مسبقاً.',
            'name.not_regex' => 'لا يمكن أن يكون اسم نوع الضريبة أرقام فقط.',
        ];
    }
}
