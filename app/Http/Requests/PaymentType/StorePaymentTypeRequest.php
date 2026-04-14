<?php

namespace App\Http\Requests\PaymentType;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentTypeRequest extends FormRequest
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
            'name' => ['required' , 'string' , 'max:255'],
            'note' => ['string' , 'max:255']
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'اسم نوع الدفع مطلوب.',
            'name.string' => 'اسم نوع الدفع يجب أن يكون نصا',
            'name.max' => 'اسم نوع الدفع لا يمكن أن يزيد عن 255 حرفاً.',

            'note.string' => 'ملاحظة نوع الدفع يجب أن تكون نصا',
            'note.max' => 'الملاحظة لا يمكن أن تزيد عن 255 حرفاً.',
        ];

    }
}
