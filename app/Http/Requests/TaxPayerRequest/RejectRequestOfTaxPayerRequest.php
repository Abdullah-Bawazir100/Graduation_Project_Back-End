<?php

namespace App\Http\Requests\TaxPayerRequest;

use Illuminate\Foundation\Http\FormRequest;

class RejectRequestOfTaxPayerRequest extends FormRequest
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
            'requestId' => ['required' , 'integer' , 'exists:requests,id']
        ];
    }
    public function messages(): array
    {
        return [
            'requestId.required' => 'الطلب مطلوب.' ,
            'requestId.integer' => 'الطلب يجب أن يكون رقما صحيحا.' ,
            'requestId.exists' => 'الطلب المحدد غير موجود.'
        ];
    }
}
