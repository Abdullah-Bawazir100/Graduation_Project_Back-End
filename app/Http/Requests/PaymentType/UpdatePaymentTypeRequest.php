<?php

namespace App\Http\Requests\PaymentType;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentTypeRequest extends FormRequest
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
            'name' => ['sometimes' , 'string' , 'max:255' , 'not_regex:/^\d+$/'],
            'note' => ['sometimes' , 'string' , 'max:255']
        ];
    }

    public function messages()
    {
        return [
            'name.string' => 'اسم نوع الدفع يجب أن يكون نصا',
            'name.max' => 'اسم نوع الدفع لا يمكن أن يزيد عن 255 حرفاً.',
            'name.not_regex' => 'لا يمكن أن يكون اسم نوع الدفع أرقام فقط.',

            'note.string' => 'ملاحظة نوع الدفع يجب أن تكون نصا',
            'note.max' => 'الملاحظة لا يمكن أن تزيد عن 255 حرفاً.',
        ];
    }
}
