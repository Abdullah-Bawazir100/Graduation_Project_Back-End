<?php

namespace App\Http\Requests\TaxPayer;

use App\Domain\TaxPayer\Enums\enFileType;
use App\Domain\User\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaxPayerRequest extends FormRequest
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
            // User fields
            'firstName'    => ['required', 'string', 'max:255' , 'not_regex:/^\d+$/'],
            'lastName'     => ['required', 'string', 'max:255' , 'not_regex:/^\d+$/'],
            'idCard'       => ['required', 'file', 'mimes:pdf' , 'max:5242880'],
            'phone'         => ['required', 'string', 'min:9' , 'unique:app_users,phone'],
            'image' => ['required' , 'image' , 'mimes:png,jpg,jpeg,gif' , 'max:5242880'],
            'role' => ['required' , Rule::in(array_map(fn($r) => $r->value, UserRole::cases()))],
            'departmentID' => ['required', 'integer', 'exists:departments,id'],

            // Taxpayer fields
            'tradeName' => 'required|string|max:255|unique:tax_payers,trade_name|not_regex:/^\d+$/',
            'commercialRecord' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240', // Max 10MB
            'activityLicense' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'tradePict' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'insuranceCard' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'propertyDocPict' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'fileType' => ['required' , Rule::in(array_map(fn($r) => $r->value, enFileType::cases()))],

            // Company fields
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

        ];
    }

    public function messages(): array
    {
        return [
            'firstName.required' => 'الأسم الأول مطلوب.',
            'firstName.string'   => 'الأسم الأول يجب أن يكون نص.',
            'firstName.max'      => 'الأسم الأول يجب ألا يتجاوز 255 حرفًا.',
            'firstName.not_regex' => 'لا يمكن أن يكون الأسم الأول أرقام فقط.',

            'lastName.required' => 'الأسم الأخير مطلوب.',
            'lastName.string'   => 'الأسم الأخير يجب أن يكون نص.',
            'lastName.max'      => 'الأسم الأخير يجب ألا يتجاوز 255 حرفًا.',
            'lastName.not_regex' => 'لا يمكن أن يكون الأسم الأخير أرقام فقط.',

            'idCard.required' => 'ملف البطاقة الشخصية مطلوب.',
            'idCard.file'     => 'يجب أن يكون الملف المرفوع صحيحًا.',
            'idCard.mimes'    => 'يجب أن يكون الملف بصيغة PDF فقط.',
            'idCard.max'     => 'يجب أن لا يتجاوز حجم الملف 5 MB.',

            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.string'   => 'رقم الهاتف يجب أن يكون نصًا.',
            'phone.min'      => 'رقم الهاتف يجب على الأقل أن يكون 9 أرقام.',
            'phone.unique'   => 'رقم الهاتف موجود بالفعل.',

            'image.required' => 'صورة الشخص مطلوبة.',
            'image.max' => 'حجم الصورة يجب ألا يتجاوز 5 MB .',

            'role.required' => 'الدور مطلوب.',
            'role.in' => 'الدور المحدد غير صالح.',

            'departmentID.required' => 'القسم مطلوب.',
            'departmentID.integer'  => 'القسم يجب أن يكون رقمًا صحيحًا.',
            'departmentID.exists'   => 'القسم المحدد غير موجود.',

            'tradeName.required' => 'السجل التجاري مطلوب.',
            'tradeName.string' => 'السجل التجاري يجب أن يكون نصا',
            'tradeName.max' => 'السجل التجاري لا يمكن أن يتجاوز 255 حرفًا.',
            'tradeName.unique' => 'السجل التجاري موجود بالفعل.',
            'tradeName.not_regex' => 'السجل التجاري لا يمكن أن يكون أرقام فقط',

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
        ];
    }
}
