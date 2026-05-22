<?php

namespace App\Http\Requests\FileMovement;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreFileMovementRequest extends FormRequest
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
            'status' => ['required' , 'string' , 'not_regex:/^\d+$/'],
            'date' => ['required' , 'string'],
            'fileId' => ['required' , 'integer' , 'exists:files,id'],
            'taxCollectorId' => ['required' , 'integer' , 'exists:tax_collectors,id'],
            'departmentId' => ['required' , 'integer' , 'exists:departments,id'],
        ];
    }

    #[Override]
    public function messages()
    {
        return [
            'status.required' => "حالة حركة الملف مطلوبة.",
            'status.string' => "حالة حركة الملف يجب أن تكون نصا.",
            'status.not_regex' => "حالة حركة الملف  لا يجب أن تكون أرقام فقط.",

            'date.required' => "تاريخ انشاء حركة الملف مطلوب.",

            'fileId.required' => "الملف مطلوب.",
            'fileId.integer' => "الملف يجب أن يكون رقما صحيحا.",
            'fileId.exists' => "الملف المحدد غير موجود.",

            'taxCollectorId.required' => "المكلف مطلوب.",
            'taxCollectorId.integer' => "المكلف يجب أن يكون رقما صحيحا.",
            'taxCollectorId.exists' => "المكلف المحدد غير موجود.",

            'departmentId.required' => "القسم مطلوب.",
            'departmentId.integer' => "القسم يجب أن يكون رقما صحيحا.",
            'departmentId.exists' => "القسم المحدد غير موجود.",
        ];
    }
}
