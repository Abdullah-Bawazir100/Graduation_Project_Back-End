<?php

namespace App\Http\Requests\TaxPayerRequest;

use App\Domain\TaxPayer\Enums\enFileType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreRequestOfTaxPayerRequest extends FormRequest
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

            'tradeName' => 'required|string|max:255|unique:tax_payers,trade_name|unique:requests,trade_name|not_regex:/^\d+$/',
            'commercialRecord' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240', // Max 10MB
            'activityLicense' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'tradePict' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'insuranceCard' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'propertyDocPict' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'fileType' => ['required' , Rule::in(array_map(fn($r) => $r->value, enFileType::cases()))],

            'articlesOfIncorporation' => [
                Rule::requiredIf(fn() => $this->fileType === enFileType::Company->value),
                'file',
                'mimes:jpeg,png,jpg,pdf',
                'max:10240',
            ],

            'govemorLicense' => [
                Rule::requiredIf(fn() => $this->fileType === enFileType::Company->value),
                'file',
                'mimes:jpeg,png,jpg,pdf',
                'max:10240',
            ],

            'partnersIDCards' => [
                Rule::requiredIf(fn() => $this->fileType === enFileType::Company->value),
                'file',
                'mimes:jpeg,png,jpg,pdf',
                'max:10240',
            ],

            'byLawsCopy' => [
                Rule::requiredIf(fn() => $this->fileType === enFileType::CharitableCompany->value),
                'file',
                'mimes:jpeg,png,jpg,pdf',
                'max:10240',
            ],

            'note' => ['nullable', 'string' , 'not_regex:/^\d+$/'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tradeName.required' => 'الاسم التجاري مطلوب.',
            'tradeName.string' => 'الاسم التجاري يجب أن يكون نصا.',
            'tradeName.max' => 'الاسم التجاري لا يمكن أن يتجاوز 255 حرفًا.',
            'tradeName.unique' => 'الاسم التجاري موجود بالفعل سواء كطلب أو كملف مكلف.',
            'tradeName.not_regex' => 'الاسم التجاري لا يمكن أن يكون أرقام فقط.',

            // commercialRecord
            'commercialRecord.required' => 'السجل التجاري مطلوب.',
            'commercialRecord.file'     => 'يجب أن يكون السجل التجاري ملفًا.',
            'commercialRecord.mimes'    => 'يجب أن يكون السجل التجاري من نوع: jpeg, png, jpg أو pdf.',
            'commercialRecord.max'      => 'يجب ألا يتجاوز حجم السجل التجاري 10 MB.',

            // activityLicense
            'activityLicense.required' => 'ترخيص مزاولة النشاط مطلوب.',
            'activityLicense.file'     => 'يجب أن يكون ترخيص مزاولة النشاط ملفًا.',
            'activityLicense.mimes'    => 'يجب أن يكون ترخيص مزاولة النشاط من نوع: jpeg, png, jpg أو pdf.',
            'activityLicense.max'      => 'يجب ألا يتجاوز حجم ترخيص مزاولة النشاط 10 MB.',

            // tradePict
            'tradePict.required' => 'قيد تسجيل الإسم التجاري مطلوب.',
            'tradePict.file'     => 'يجب أن يكون قيد تسجيل الإسم التجاري ملفًا.',
            'tradePict.mimes'    => 'يجب أن يكون قيد تسجيل الإسم التجاري من نوع: jpeg, png, jpg أو pdf.',
            'tradePict.max'      => 'يجب ألا يتجاوز حجم قيد تسجيل الإسم التجاري 10 MB.',

            'fileType.required' => 'نوع الملف مطلوب',
            'fileType.in' => 'يجب أن يكون نوع الملف واحدًا من: ملف فرد، ملف شركة، ملف شركة خيرية',

            'articlesOfIncorporation.required' => 'عقد التأسيس مطلوب في ملف الشركة.',
            'articlesOfIncorporation.file'     => 'يجب أن يكون عقد التأسيس ملفا صالحا.',
            'articlesOfIncorporation.mimes'    => 'يجب أن يكون عقد التأسيس من نوع: jpeg, png, jpg أو pdf.',
            'articlesOfIncorporation.max'      => 'يجب ألا يتجاوز حجم عقد التأسيس 10 MB.',

            'govemorLicense.required' => 'ترخيص المحافظ مطلوب في ملف الشركة.',
            'govemorLicense.file'     => 'يجب أن يكون ترخيص المحافظ ملفًا.',
            'govemorLicense.mimes'    => 'يجب أن يكون ترخيص المحافظ من نوع: jpeg, png, jpg أو pdf.',
            'govemorLicense.max'      => 'يجب ألا يتجاوز حجم ترخيص المحافظ 10 MB.',

            'partnersIDCards.required' => 'بطائق الشركاء مطلوبة في ملف الشركة.',
            'partnersIDCards.file'     => 'يجب أن تكون بطائق الشركاء ملفات.',
            'partnersIDCards.mimes'    => 'يجب أن تكون بطائق الشركاء من نوع: jpeg, png, jpg أو pdf.',
            'partnersIDCards.max'      => 'يجب ألا يتجاوز حجم بطائق الشركاء 10 MB.',

            'byLawsCopy.required' => 'الصورة من النظام الأساسي  مطلوبة.',
            'byLawsCopy.file' => 'الصورة من النظام الأساسي يجب أن تكون ملفا صالحا.',
            'byLawsCopy.mimes' => 'الصورة من النظام الأساسي يجب ان تكون من نوع jpeg,png,jpg,pdf.',
            'byLawsCopy.max' => 'الصورة من النظام الأساسي يجب الا تتجاوز 10MB.',

            'note.string' => 'الملاحظة يجب ان تكون نصا.',
            'note.not_regex' => 'الملاحظة يجب ان لا تكون أرقام فقط.'
        ];
    }
}
