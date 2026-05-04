<?php

namespace App\Http\Requests\District;

use Illuminate\Foundation\Http\FormRequest;

class StoreDistrictRequest extends FormRequest
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
            'name' => ['required' , 'string' , 'max:255' , 'not_regex:/^\d+$/' , 'unique:districts,name'],
            'regionID' => ['required' , 'integer' , 'exists:regions,id']
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'اسم المنطقة مطلوب.',
            'name.string' => 'اسم المنطقة يجب ان يكون نصاً.',
            'name.max' => 'اسم المنطقة لا يمكن ان يزيد عن 255 حرفا.',
            'name.not_regex' => 'لا يمكن أن يكون اسم المنطقة أرقام فقط.',
            'name.unique' => 'اسم الحي موجود بالفعل.',

            'regionID.required' => 'المنطقة مطلوبة.',
            'regionID.integer' => 'المنطقة يجب أن تكون رقم صحيحًا',
            'regionID.exists'   => 'المنطقة المحددة غير موجودة.',

        ];
    }
}
