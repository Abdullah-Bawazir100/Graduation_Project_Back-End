<?php

namespace App\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;

class StoreFileRequest extends FormRequest
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
            'taxNumber' => ['nullable', 'string', 'max:255'],
            'inventoryNumber' => ['required', 'string', 'max:255' , 'unique:files,inventory_number'],
            'activityStartDate' => ['nullable', 'string'],
            'docsCount' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string'],
            'taxPayerId' => ['required', 'integer', 'exists:tax_payers,id'],
            'departmentId' => ['required', 'integer', 'exists:departments,id'],
            'fileStatusId' => ['required', 'integer', 'exists:file_status,id'],
            'activityTypeId' => ['required', 'integer', 'exists:activity_types,id'],
            'paymentTypeId' => ['required', 'integer', 'exists:payment_types,id'],
            'regionId' => ['required', 'integer', 'exists:regions,id'],
            'districtId' => ['required', 'integer', 'exists:districts,id'],
            'requestId' => ['nullable', 'integer', 'exists:requests,id'],
        ];
    }

    public function messages()
    {
        return [
            'taxNumber.string' => 'الرقم الضريبي يجب أن يكون نصاً.',
            'taxNumber.max' => 'الرقم الضريبي يجب أن لا يتجاوز 255 حرف.',

            'inventoryNumber.required' => 'رقم الحصري مطلوب.',
            'inventoryNumber.string' => 'الرقم الحصري يجب أن يكون نصاً.',
            'inventoryNumber.max' => 'الرقم الحصري يجب أن لا يتجاوز 255 حرف.',
            'inventoryNumber.unique' => 'الرقم الحصري موجود بالفعل.',

            'activityStartDate.date' => 'تاريخ بدء النشاط يجب أن يكون تاريخاً صحيحاً.',

            'docsCount.required' => 'عدد المستندات مطلوب.',
            'docsCount.integer' => 'عدد المستندات يجب أن يكون رقماً صحيحاً.',
            'docsCount.min' => 'عدد المستندات يجب أن يكون 0 على الأقل.',

            'note.string' => 'الملاحظة يجب أن تكون نصاً.',

            'taxPayerId.required' => 'معرف المكلف مطلوب.',
            'taxPayerId.integer' => 'معرف المكلف يجب أن يكون رقماً صحيحاً.',
            'taxPayerId.exists' => 'المكلف المحدد غير موجود.',

            'departmentId.required' => 'معرف القسم مطلوب.',
            'departmentId.integer' => 'معرف القسم يجب أن يكون رقماً صحيحاً.',
            'departmentId.exists' => 'القسم المحدد غير موجود.',

            'fileStatusId.required' => 'معرف حالة الملف مطلوب.',
            'fileStatusId.integer' => 'معرف حالة الملف يجب أن يكون رقماً صحيحاً.',
            'fileStatusId.exists' => 'حالة الملف المحددة غير موجودة.',

            'activityTypeId.required' => 'معرف نوع النشاط مطلوب.',
            'activityTypeId.integer' => 'معرف نوع النشاط يجب أن يكون رقماً صحيحاً.',
            'activityTypeId.exists' => 'نوع النشاط المحدد غير موجود.',

            'paymentTypeId.required' => 'معرف نوع الدفع مطلوب.',
            'paymentTypeId.integer' => 'معرف نوع الدفع يجب أن يكون رقماً صحيحاً.',
            'paymentTypeId.exists' => 'نوع الدفع المحدد غير موجود.',

            'regionId.required' => 'معرف المنطقة مطلوب.',
            'regionId.integer' => 'معرف المنطقة يجب أن يكون رقماً صحيحاً.',
            'regionId.exists' => 'المنطقة المحددة غير موجودة.',

            'districtId.required' => 'معرف الحي مطلوب.',
            'districtId.integer' => 'معرف الحي يجب أن يكون رقماً صحيحاً.',
            'districtId.exists' => 'الحي المحدد غير موجود.',

            'requestId.integer' => 'معرف الطلب يجب أن يكون رقماً صحيحاً.',
            'requestId.exists' => 'الطلب المحدد غير موجود.',
        ];
    }
}
