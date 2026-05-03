<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;

class MoveUsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'departmentID' => 'required|integer|exists:departments,id',
        ];
    }

    public function messages(): array
    {
        return [
            'departmentID.required' => 'القسم الجديد مطلوب.',
            'departmentID.integer' => 'يجب أن يكون القسم الجديد رقمًا صحيحًا.',
            'departmentID.exists' => 'القسم الجديد غير موجود.',
        ];
    }
}
