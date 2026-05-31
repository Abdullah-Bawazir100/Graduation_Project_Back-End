<?php

namespace App\Http\Requests\Notification;

use App\Domain\Notification\Enums\enNotificationType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNotificationRequest extends FormRequest
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
            'title' => ['required' , 'string' , 'not_regex:/^\d+$/'],
            'description' => ['required' , 'string' , 'not_regex:/^\d+$/'],
            'notificationType' => ['required' , Rule::in(array_map(fn($r) => $r->value , enNotificationType::cases()))],
            'receiverPhone' => [
                'nullable',
                Rule::requiredIf(
                fn() => $this->notificationType === enNotificationType::Special->value),
                'string',
                'exists:app_users,phone',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الإشعار مطلوب.',
            'title.string' => 'عنوان الإشعار يجب أن يكون نص.',
            'title.not_regex' => 'عنوان الإشعار لا يجب أن يكون أرقام فقط.',

            'description.required' => 'محتوى الإشعار مطلوب.',
            'description.string' => 'محتوى الإشعار يجب أن يكون نص.',
            'description.not_regex' => 'محتوى الإشعار لا يجب أن يكون أرقام فقط.',

            'notificationType.required' => 'نوع الإشعار مطلوب.',
            'notificationType.in' => 'نوع الإشعار يجب أن يكون واحد من : عامة , مستخدمي النظام , المكلفين , خاصة.',

            'receiverPhone.required' => 'مستلم الإشعار مطلوب.',
            'receiverPhone.exists' => 'مستلم الإشعار غير موجود.',
        ];
    }
}
