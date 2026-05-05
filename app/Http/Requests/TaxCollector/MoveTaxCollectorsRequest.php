<?php

namespace App\Http\Requests\TaxCollector;

use Illuminate\Foundation\Http\FormRequest;

class MoveTaxCollectorsRequest extends FormRequest
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
            'newJobTypeId' => 'required|integer|exists:job_types,id'
        ];
    }

    public function messages(): array
    {
        return [
            'newJobTypeId.required' => 'نوع الوظيفة الجديد مطلوب.',
            'newJobTypeId.integer' => 'نوع الوظيفة يجب أن يكون رقما صحيحا.',
            'newJobTypeId.exists' => 'نوع الوظيفة غير موجود.',
        ];
    }
}
