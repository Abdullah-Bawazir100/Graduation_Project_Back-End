<?php

namespace App\Http\Requests\TaxCollector;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaxCollectorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fullName' => 'required|string|max:255|not_regex:/^\d+$/',
            'idCard' => 'required|file|mimes:pdf|max:5120', // 5MB max, PDF only
            'phone' => 'required|string|min:9|unique:tax_collectors,phone',
            'jobTypeId' => 'required|integer|exists:job_types,id',
            'deptID' => 'required|integer|exists:departments,id',
        ];
    }

    public function messages(): array
    {
        return [
            'fullName.required' => 'الاسم الكامل مطلوب',
            'fullName.string' => 'يجب أن يكون الاسم الكامل نصًا',
            'fullName.max' => 'لا يمكن أن يتجاوز الاسم الكامل 255 حرفًا',
            'fullName.not_regex' => 'لا يمكن أن يكون الأسم الكامل أرقام فقط.',


            'idCard.required' => '.بطاقة الهوية مطلوبة',
            'idCard.file' => 'يجب أن يكون الملف ملف صالح',
            'idCard.mimes' => 'يجب أن يكون نوع الملف PDF',
            'idCard.max' => 'لا يمكن أن يتجاوز حجم الملف 5 MB.',

            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.string' => 'يجب أن يكون رقم الهاتف نصًا',
            'phone.min'      => 'رقم الهاتف يجب على الأقل أن يكون 9 أرقام.',
            'phone.unique' => 'هذا الرقم الهاتف موجود بالفعل',

            'jobTypeId.required' => 'نوع الوظيفة مطلوب',
            'jobTypeId.integer' => 'يجب أن يكون نوع الوظيفة رقمًا',
            'jobTypeId.exists' => 'نوع الوظيفة المحدد غير موجود',

            'deptID.required' => 'القسم مطلوب',
            'deptID.integer' => 'يجب أن يكون القسم رقمًا',
            'deptID.exists' => 'القسم المحدد غير موجود',
        ];
    }
}
