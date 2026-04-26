<?php

namespace App\Http\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
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
            'regionID' => ['sometimes', 'exists:regions,id'],
            'districtID' => ['sometimes', 'exists:districts,id']
        ];
    }

    public function messages(): array
    {
        return [
            'regionID.exists' => ' المنطقة غير موجودة.',
            'districtID.exists' => 'الحي غير موجود.'
        ];
    }
}
