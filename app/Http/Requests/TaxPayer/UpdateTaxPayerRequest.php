<?php

namespace App\Http\Requests\TaxPayer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaxPayerRequest extends FormRequest
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
            'commercialRecord' => 'sometimes|file|mimes:jpeg,png,jpg,pdf|max:10240', // Max 10MB
            'activityLicense' => 'sometimes|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'tradePict' => 'sometimes|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'insuranceCard' => 'sometimes|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'propertyDocPict' => 'sometimes|file|mimes:jpeg,png,jpg,pdf|max:10240',
        ];
    }

    public function messages()
    {
        return [

            'commercial_record.file' => 'يجب أن يكون السجل تجاري ملفًا',
            'activity_license.file' => 'يجب أن يكون ترخيص مزاولة النشاط ملفًا',
            'trade_pict.file' => ' أن يكون قيد تسجيل الإسم التجاري ملفًا',
            'insurance_card.file' => 'يجب أن تكون البطاقة التأمينية ملفًا',
            'property_doc_pict.file' => 'يجب أن يكو يكون عقد الإيجار ملفًا',

            'commercial_record.mimes' => 'يجب أن يكون السجل من نوع: jpeg, png, jpg أو pdf',
            'activity_license.mimes' => 'يجب أن يكون ترخيص مزاولة النشاط من نوع: jpeg, png, jpg أو pdf',
            'trade_pict.mimes' => 'يجب أن يكون قيد تسجيل الإسم التجاري من نوع: jpeg, png, jpg أو pdf',
            'insurance_card.mimes' => 'يجب أن تكون البطاقة التأمينية من نوع: jpeg, png, jpg أو pdf',
            'property_doc_pict.mimes' => 'يجب أن يكون عقد الإيجار من نوع: jpeg, png, jpg أو pdf',

            'commercial_record.max' => 'يجب ألا يتجاوز حجم السجل التجاري 10 MB',
            'activity_license.max' => 'يجب ألا يتجاوز حجم ترخيص مزاولة النشاط 10 MB',
            'trade_pict.max' => 'يجب ألا يتجاوز حجم قيد تسجيل الإسم التجاري 10 MB',
            'insurance_card.max' => 'يجب ألا يتجاوز حجم البطاقة التأمينية 10 MB',
            'property_doc_pict.max' => 'يجب ألا يتجاوز حجم عقد الإيجار 10 MB',

        ];
    }
}
