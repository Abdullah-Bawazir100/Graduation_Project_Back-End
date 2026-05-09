<?php

namespace App\Http\Requests\TaxPayerMobile;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class UpdateTaxPayerMobileRequest extends FormRequest
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
            'firstName' => [
                'sometimes',
                'string',
                'max:255',
                'not_regex:/^\d+$/'
            ],

            'lastName' => [
                'sometimes',
                'string',
                'max:255',
                'not_regex:/^\d+$/'
            ],

            'idCard' => [
                'sometimes',
                'file',
                'mimes:pdf',
                'max:5242880'
            ],

            'phone' => [
                'sometimes',
                'string',
                'min:9',
            ],

            'userName' => [
                'sometimes',
                'string',
                'min:3',
                'not_regex:/^\d+$/'
            ],

            'password' => [
                'sometimes',
                'string',
                'min:8',
                'confirmed'
            ],

            'image' => [
                'sometimes',
                'image',
                'mimes:png,jpg,jpeg,gif',
                'max:5012'
            ],

            'departmentID' => [
                'sometimes',
                'integer',
                'exists:departments,id'
            ],
        ];
    }

    #[Override]
    public function messages()
    {
        return [

            'firstName.string'   => 'الأسم الأول يجب أن يكون نص.',
            'firstName.max'      => 'الأسم الأول يجب ألا يتجاوز 255 حرفًا.',
            'firstName.not_regex' => 'لا يمكن أن يكون الأسم الأول أرقام فقط.',

            'lastName.string'   => 'الأسم الأخير يجب أن يكون نص.',
            'lastName.max'      => 'الأسم الأخير يجب ألا يتجاوز 255 حرفًا.',
            'lastName.not_regex' => 'لا يمكن أن يكون الأسم الأخير أرقام فقط.',

            'idCard.file'     => 'يجب أن يكون الملف المرفوع صحيحًا.',
            'idCard.mimes'    => 'يجب أن يكون الملف بصيغة PDF فقط.',
            'idCard.max'      => 'يجب أن لا يتجاوز حجم الملف 5 MB.',

            'phone.string'   => 'رقم الهاتف يجب أن يكون نصًا.',
            'phone.min'      => 'رقم الهاتف يجب على الأقل أن يكون 9 أرقام.',
            'phone.unique'   => 'رقم الهاتف موجود بالفعل.',

            'userName.string'   => 'اسم المستخدم يجب أن يكون نصًا.',
            'userName.min'      => 'اسم المستخدم يجب ألا يقل عن 3 أحرف.',
            'userName.unique'   => 'اسم المستخدم مستخدم بالفعل.',
            'userName.not_regex' => 'لا يمكن أن يكون أسم المستخدم أرقام فقط.',

            'password.string'    => 'كلمة المرور يجب أن تكون نصًا.',
            'password.min'       => 'كلمة المرور يجب ألا تقل عن 8 أحرف.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',

            'image.image' => 'الملف المرفوع يجب أن يكون صورة.',
            'image.mimes' => 'الصورة يجب أن تكون بصيغة png أو jpg أو jpeg أو gif.',
            'image.max'   => 'حجم الصورة يجب ألا يتجاوز 5 MB.',

            'departmentID.integer' => 'معرف القسم يجب أن يكون رقم صحيح.',
            'departmentID.exists' => 'القسم غير موجود.',
        ];
    }
}
