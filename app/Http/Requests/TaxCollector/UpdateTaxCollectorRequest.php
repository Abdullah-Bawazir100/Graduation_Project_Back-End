<?php

namespace App\Http\Requests\TaxCollector;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaxCollectorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fullName' => 'sometimes|string|max:255|not_regex:/^\d+$/',
            'idCard' => 'sometimes|file|mimes:pdf|max:5120', // 5MB max, PDF only
            'phone' => 'sometimes|string|min:9|unique:tax_collectors,phone',
            'jobTypeId' => 'sometimes|integer|exists:job_types,id',
            'deptID' => 'sometimes|integer|exists:departments,id',
        ];
    }

    public function messages(): array
    {
        return [
            'fullName.string' => 'يجب أن يكون الاسم الكامل نصًا',
            'fullName.max' => 'لا يمكن أن يتجاوز الاسم الكامل 255 حرفًا',
            'fullName.not_regex' => 'لا يمكن أن يكون الأسم الكامل أرقام فقط.',

            'idCard.file' => 'يجب أن يكون الملف ملف صالح',
            'idCard.mimes' => 'يجب أن يكون نوع الملف PDF',
            'idCard.max' => 'لا يمكن أن يتجاوز حجم الملف 5 MB',

            'phone.string' => 'يجب أن يكون رقم الهاتف نصًا',
            'phone.min'      => 'رقم الهاتف يجب على الأقل أن يكون 9 أرقام.',
            'phone.unique' => 'هذا الرقم الهاتف موجود بالفعل',


            'jobTypeId.integer' => 'نوع الوظيفة يجب أن يكون رقما صحيحا',
            'jobTypeId.exists' => 'نوع الوظيفة المحدد غير موجود',

            'deptID.integer' => 'القسم يجب أن يكون رقما صحيحا',
            'deptID.exists' => 'القسم المحدد غير موجود',
        ];
    }
}
