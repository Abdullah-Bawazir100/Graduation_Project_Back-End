<?php

namespace App\Http\Requests\TaxPayerRequest;

use App\Domain\Request\Enums\enRequestStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class RejectRequestOfTaxPayerRequest extends FormRequest
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
            'requestId' => ['required' , 'integer' , 'exists:requests,id'],
            'note' => [
                'required',
                'string',
                'not_regex:/^\d+$/'
            ],
        ];
    }
    #[Override]
    public function messages()
    {
        return [
            'requestId.required' => 'الطلب مطلوب.',
            'requestId.integer' => 'الطلب يجب أن يكون رقما صحيحا.',
            'requestId.exists' => 'الطلب المحدد غير موجود.',

            'note.required' => 'الملاحظة مطلوبة.',
            'note.string' => 'الملاحظة يجب أن تكون نصا.',
            'note.not_regex' => 'الملاحظة لا يجب أن تكون أرقام فقط.'
        ];
    }
}
