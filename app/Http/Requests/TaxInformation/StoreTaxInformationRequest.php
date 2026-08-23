<?php

namespace App\Http\Requests\TaxInformation;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaxInformationRequest extends FormRequest
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
            'taxAmount' => ['required', 'string' , 'min:0'],
            'lastPayment' => ['required', 'string'],
            'attachment' => ['nullable' , 'file' , 'mimes:jpeg,png,jpg,pdf' , 'max:10240',],
            'taxTypeId' => ['required', 'integer', 'exists:tax_types,id'],
            'fileId' => ['required', 'integer', 'exists:files,id'],
        ];
    }

    public function messages(): array
    {
        return [
            // tax_amount
            'taxAmount.required' => 'قيمة الضريبة مطلوبة.',
            'taxAmount.min' => 'قيمة الضريبة يجب أن تكون أكبر من أو تساوي صفر.',

            // last_payment
            'lastPayment.required' => 'اخر دفع مطلوب.',

            // attachment
            'attachment.file' => 'المرفقات الأخرى يجب أن تكون ملفا',
            'attachment.mimes'    => 'يجب أن تكون المرفقات الأخرى من نوع: jpeg, png, jpg أو pdf.',
            'attachment.max'      => 'يجب ألا يتجاوز حجم المرفقات الأخرى 10 MB.',


            // tax_type_id
            'taxTypeId.required' => 'نوع الضريبة مطلوب.',
            'taxTypeId.integer' => 'نوع الضريبة يجب أن يكون رقمًا صحيحًا.',
            'taxTypeId.exists' => 'نوع الضريبة المحدد غير موجود.',

            // fileId
            'fileId.required' => 'الملف مطلوب.',
            'fileId.integer' => 'معرف الملف يجب أن يكون رقمًا صحيحًا.',
            'fileId.exists' => 'الملف المحدد غير موجود.',
        ];
    }
}
