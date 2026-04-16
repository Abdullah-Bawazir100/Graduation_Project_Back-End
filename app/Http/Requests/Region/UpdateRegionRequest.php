<?php

namespace App\Http\Requests\Region;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRegionRequest extends FormRequest
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
            'name.string' => 'اسم المنطقة يجب ان يكون نصاً.',
            'name.max' => 'اسم المنطقة لا يمكن ان يزيد عن 255 حرفا.',
            'name.not_regex' => 'لا يمكن أن يكون اسم المنطقة أرقام فقط.',
        ];
    }
}
