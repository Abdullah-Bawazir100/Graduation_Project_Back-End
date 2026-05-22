<?php

namespace App\Http\Requests\FileMovement;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFileMovementRequest extends FormRequest
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
            'status' => ['sometimes' , 'string' , 'not_regex:/^\d+$/'],
            'date' => ['sometimes' , 'string'],
            'fileId' => ['sometimes' , 'integer' , 'exists:files,id'],
            'taxCollectorId' => ['sometimes' , 'integer' , 'exists:tax_collectors,id'],
            'departmentId' => ['sometimes' , 'integer' , 'exists:departments,id'],
        ];
    }

    public function messages()
    {
        return [
            'status.string' => "حالة حركة الملف يجب أن تكون نصا.",
            'status.not_regex' => "حالة حركة الملف  لا يجب أن تكون أرقام فقط.",

            'fileId.integer' => "الملف يجب أن يكون رقما صحيحا.",
            'fileId.exists' => "الملف المحدد غير موجود.",

            'taxCollectorId.integer' => "المكلف يجب أن يكون رقما صحيحا.",
            'taxCollectorId.exists' => "المكلف المحدد غير موجود.",

            'departmentId.integer' => "القسم يجب أن يكون رقما صحيحا.",
            'departmentId.exists' => "القسم المحدد غير موجود.",
        ];
    }
}
