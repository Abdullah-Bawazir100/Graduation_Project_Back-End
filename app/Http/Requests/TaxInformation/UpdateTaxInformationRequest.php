<?php

namespace App\Http\Requests\TaxInformation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaxInformationRequest extends FormRequest
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
            'taxAmount' => ['sometimes', 'string' , 'min:0'],
            'lastPayment' => ['sometimes', 'string'],
            'attachment' => ['sometimes' , 'file' , 'mimes:jpeg,png,jpg,pdf' , 'max:10240',],
            'taxTypeId' => ['sometimes', 'integer', 'exists:tax_types,id'],
            'taxPayerId' => ['sometimes', 'integer', 'exists:tax_payers,id'],
        ];
    }
    public function messages(): array
    {
        return [
            // tax_amount
            'taxAmount.min' => 'قيمة الضريبة يجب أن تكون أكبر من أو تساوي صفر.',

            // attachment
            'attachment.file' => 'المرفقات الأخرى يجب أن تكون ملفا',
            'attachment.mimes'    => 'يجب أن تكون المرفقات الأخرى من نوع: jpeg, png, jpg أو pdf.',
            'attachment.max'      => 'يجب ألا يتجاوز حجم المرفقات الأخرى 10 MB.',

            // tax_type_id
            'taxTypeId.integer' => 'نوع الضريبة يجب أن يكون رقمًا صحيحًا.',
            'taxTypeId.exists' => 'نوع الضريبة المحدد غير موجود.',

            // tax_payer_id
            'taxPayerId.integer' => 'معرف المكلف يجب أن يكون رقمًا صحيحًا.',
            'taxPayerId.exists' => 'المكلف المحدد غير موجود.',
        ];
    }
}
