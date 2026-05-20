<?php

namespace App\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFileRequest extends FormRequest
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
            'taxNumber' => ['sometimes', 'nullable', 'string', 'max:255'],

            'inventoryNumber' => [
                'sometimes',
                'string',
                'max:255',
                'unique:files,inventory_number'
            ],

            'activityStartDate' => ['sometimes', 'string'],

            'docsCount' => ['sometimes', 'integer', 'min:0'],

            'note' => ['sometimes', 'nullable', 'string'],

            'taxPayerId' => ['sometimes', 'integer', 'exists:tax_payers,id'],

            'departmentId' => ['sometimes', 'integer', 'exists:departments,id'],

            'fileStatusId' => ['sometimes', 'integer', 'exists:file_status,id'],

            'activityTypeId' => ['sometimes', 'integer', 'exists:activity_types,id'],

            'paymentTypeId' => ['sometimes', 'integer', 'exists:payment_types,id'],

            'regionId' => ['sometimes', 'integer', 'exists:regions,id'],

            'districtId' => ['sometimes', 'integer', 'exists:districts,id'],
        ];
    }
    public function messages(): array
    {
        return [
            'taxNumber.string' => 'الرقم الضريبي يجب أن يكون نصاً.',
            'taxNumber.max' => 'الرقم الضريبي يجب أن لا يتجاوز 255 حرف.',

            'inventoryNumber.string' => 'الرقم الحصري يجب أن يكون نصاً.',
            'inventoryNumber.max' => 'الرقم الحصري يجب أن لا يتجاوز 255 حرف.',
            'inventoryNumber.unique' => 'الرقم الحصري موجود بالفعل.',

            'docsCount.integer' => 'عدد المستندات يجب أن يكون رقماً صحيحاً.',
            'docsCount.min' => 'عدد المستندات يجب أن يكون 0 على الأقل.',

            'note.string' => 'الملاحظة يجب أن تكون نصاً.',

            'taxPayerId.integer' => 'معرف المكلف يجب أن يكون رقماً صحيحاً.',
            'taxPayerId.exists' => 'المكلف المحدد غير موجود.',

            'departmentId.integer' => 'معرف القسم يجب أن يكون رقماً صحيحاً.',
            'departmentId.exists' => 'القسم المحدد غير موجود.',

            'fileStatusId.integer' => 'معرف حالة الملف يجب أن يكون رقماً صحيحاً.',
            'fileStatusId.exists' => 'حالة الملف المحددة غير موجودة.',

            'activityTypeId.integer' => 'معرف نوع النشاط يجب أن يكون رقماً صحيحاً.',
            'activityTypeId.exists' => 'نوع النشاط المحدد غير موجود.',

            'paymentTypeId.integer' => 'معرف نوع الدفع يجب أن يكون رقماً صحيحاً.',
            'paymentTypeId.exists' => 'نوع الدفع المحدد غير موجود.',

            'regionId.integer' => 'معرف المنطقة يجب أن يكون رقماً صحيحاً.',
            'regionId.exists' => 'المنطقة المحددة غير موجودة.',

            'districtId.integer' => 'معرف الحي يجب أن يكون رقماً صحيحاً.',
            'districtId.exists' => 'الحي المحدد غير موجود.',
        ];
    }
}
