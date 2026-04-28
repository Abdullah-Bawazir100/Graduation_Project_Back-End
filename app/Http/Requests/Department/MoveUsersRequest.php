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
            'department_id' => 'required|integer|exists:departments,id',
        ];
    }

    public function messages(): array
    {
        return [
            'department_id.required' => 'القسم الجديد مطلوب.',
            'department_id.integer' => 'يجب أن يكون القسم الجديد رقمًا صحيحًا.',
            'department_id.exists' => 'القسم الجديد غير موجود.',
        ];
    }
}
