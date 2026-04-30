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
            'fullName' => 'required|string|max:255',
            'idCard' => 'required|file|mimes:pdf|max:5120', // 5MB max, PDF only
            'phone' => 'required|string|max:20',
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

            'idCard.required' => '.بطاقة الهوية مطلوبة',
            'idCard.file' => 'يجب أن يكون الملف ملف صالح',
            'idCard.mimes' => 'يجب أن يكون نوع الملف PDF',
            'idCard.max' => 'لا يمكن أن يتجاوز حجم الملف 5 MB.',

            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.string' => 'يجب أن يكون رقم الهاتف نصًا',
            'phone.max' => 'لا يمكن أن يتجاوز رقم الهاتف 20 حرفًا',

            'jobTypeId.required' => 'نوع الوظيفة مطلوب',
            'jobTypeId.integer' => 'يجب أن يكون نوع الوظيفة رقمًا',
            'jobTypeId.exists' => 'نوع الوظيفة المحدد غير موجود',

            'deptID.required' => 'القسم مطلوب',
            'deptID.integer' => 'يجب أن يكون القسم رقمًا',
            'deptID.exists' => 'القسم المحدد غير موجود',
        ];
    }
}
