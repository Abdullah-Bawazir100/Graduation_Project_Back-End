<?php

namespace App\Http\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
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
            'regionID' => ['required', 'integer' , 'exists:regions,id'],
            'districtID' => ['required', 'integer' , 'exists:districts,id']
        ];
    }

    public function messages(): array
    {
        return [
            'regionID.required' => 'المنطقة مطلوبة.',
            'regionID.integer'  => 'يجب أن يكون رقم المنطقة رقما صحيحا.',
            'regionID.exists' => 'المنطقة غير موجودة.',

            'districtID.required' => 'الحي مطلوب.',
            'districtID.integer'  => 'يجب أن يكون رقم الحي رقما صحيحا.',
            'districtID.exists' => 'الحي غير موجود.'
        ];
    }
}
