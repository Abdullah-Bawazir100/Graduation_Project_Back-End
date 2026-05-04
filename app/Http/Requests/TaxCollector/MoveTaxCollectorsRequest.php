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
            'new_departmentId' => 'required|integer|exists:departments,id'
        ];
    }
    public function messages(): array
    {
        return [
            'new_departmentId.required' => 'القسم الجديد مطلوب.',
            'new_departmentId.integer' => 'يجب أن يكون القسم الجديد رقمًا صحيحًا.',
            'new_departmentId.exists' => 'القسم الجديد غير موجود.',
        ];
    }
}
