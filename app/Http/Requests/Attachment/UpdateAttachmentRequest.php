<?php

namespace App\Http\Requests\Attachment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttachmentRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:255' , 'not_regex:/^\d+$/'],
            'attachmentFile' => ['sometimes', 'file', 'mimes:pdf,jpg,jpeg,png' , 'max:10240'],
        ];
    }

    public function messages()
    {
        return [
            'title.string' => 'العنوان يجب أن يكون نصا.',
            'title.not_regex' => 'العنوان يجب أن لا يكون أرقام فقط.',

            'attachment.file' => 'المرفق  يجب أن يكون ملفا.',
            'attachment.max' => 'المرفق  يجب أن لا يتجاوز 10 MB.',
            'attachment.mimes' => 'المرفق يجب أن يكون ملف من نوع (jpg, jpeg, png, pdf).',

        ];
    }
}
