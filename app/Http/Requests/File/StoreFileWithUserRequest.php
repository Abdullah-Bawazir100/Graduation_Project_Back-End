<?php

namespace App\Http\Requests\File;

use App\Domain\User\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class StoreFileWithUserRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // User fields
            'firstName'    => ['required', 'string', 'max:255' , 'not_regex:/^\d+$/'],
            'lastName'     => ['required', 'string', 'max:255' , 'not_regex:/^\d+$/'],
            'idCard'       => ['required', 'file', 'mimes:pdf' , 'max:5242880'],
            'phone'         => ['required', 'digits:9', 'regex:/^7[0-9]{8}$/', 'unique:app_users,phone'],
            'image' => ['required' , 'image' , 'mimes:png,jpg,jpeg,gif' , 'max:5242880'],
            'role' => ['required' , Rule::in(array_map(fn($r) => $r->value, UserRole::cases()))],
            'departmentID' => ['required', 'integer', 'exists:departments,id'],


            // File fields
            'taxNumber' => ['nullable', 'string', 'max:255'],
            'inventoryNumber' => ['required', 'string', 'max:255' , 'unique:files,inventory_number'],
            'activityStartDate' => ['nullable', 'string'],
            'docsCount' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string'],
            'departmentId' => ['required', 'integer', 'exists:departments,id'],
            'fileStatusId' => ['required', 'integer', 'exists:file_status,id'],
            'activityTypeId' => ['required', 'integer', 'exists:activity_types,id'],
            'paymentTypeId' => ['required', 'integer', 'exists:payment_types,id'],
            'requestId' => ['nullable', 'integer', 'exists:requests,id'],
        ];
    }

    #[Override]
    public function messages()
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
            'phone.digits'   => 'رقم الهاتف يجب أن يتكون من 9 أرقام فقط.',
            'phone.regex'    => 'رقم الهاتف يجب أن يبدأ بالرقم 7 ويتكون من 9 أرقام.',
            'phone.unique'   => 'رقم الهاتف موجود بالفعل.',

            'image.required' => 'صورة الشخص مطلوبة.',
            'image.max' => 'حجم الصورة يجب ألا يتجاوز 5 MB .',

            'role.required' => 'الدور مطلوب.',
            'role.in' => 'الدور المحدد غير صالح.',

            'departmentID.required' => 'القسم مطلوب.',
            'departmentID.integer'  => 'القسم يجب أن يكون رقمًا صحيحًا.',
            'departmentID.exists'   => 'القسم المحدد غير موجود.',

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

            // 'userId.required' => 'معرف المستخدم مطلوب.',
            // 'userId.integer' => 'معرف المستخدم يجب أن يكون رقماً صحيحاً.',
            // 'userId.exists' => 'المستخدم المحدد غير موجود.',

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

            'requestId.integer' => 'معرف الطلب يجب أن يكون رقماً صحيحاً.',
            'requestId.exists' => 'الطلب المحدد غير موجود.',
        ];
    }
}
