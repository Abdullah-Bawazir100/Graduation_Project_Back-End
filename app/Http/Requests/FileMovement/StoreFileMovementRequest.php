<?php

namespace App\Http\Requests\FileMovement;

use App\Domain\FileMovement\Enums\enFileMovement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
            'status' => ['required' , Rule::in(array_map(fn($r) => $r->value, enFileMovement::cases()))],
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
            'status.required' => 'حالة الملف مطلوبة.',
            'status.in' => 'يجب أن تكون حالة الملف واحدة من : داخل الأرشيف ، خارج الأرشيف ، مفقود.',

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
