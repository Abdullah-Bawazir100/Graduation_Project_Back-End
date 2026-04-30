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
            'fullName' => 'sometimes|required|string|max:255',
            'idCard' => 'sometimes|required|file|mimes:pdf|max:5120', // 5MB max, PDF only
            'phone' => 'sometimes|required|string|max:20',
            'jobTypeId' => 'sometimes|integer|exists:job_types,id',
            'deptID' => 'sometimes|integer|exists:departments,id',
        ];
    }

    public function messages(): array
    {
        return [
            'fullName.string' => 'يجب أن يكون الاسم الكامل نصًا',
            'fullName.max' => 'لا يمكن أن يتجاوز الاسم الكامل 255 حرفًا',

            'idCard.required' => 'ملف بطاقة الهوية مطلوب',
            'idCard.file' => 'يجب أن يكون الملف ملف صالح',
            'idCard.mimes' => 'يجب أن يكون نوع الملف PDF',
            'idCard.max' => 'لا يمكن أن يتجاوز حجم الملف 5 MB',

            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.string' => 'يجب أن يكون رقم الهاتف نصًا',
            'phone.max' => 'لا يمكن أن يتجاوز رقم الهاتف 20 حرفًا',

            'jobTypeId.required' => 'نوع الوظيفة مطلوب',
            'jobTypeId.integer' => 'نوع الوظيفة يجب أن يكون رقما صحيحا',
            'jobTypeId.exists' => 'نوع الوظيفة المحدد غير موجود',

            'deptID.required' => 'القسم مطلوب',
            'deptID.integer' => 'القسم يجب أن يكون رقما صحيحا',
            'deptID.exists' => 'القسم المحدد غير موجود',
        ];
    }
}
