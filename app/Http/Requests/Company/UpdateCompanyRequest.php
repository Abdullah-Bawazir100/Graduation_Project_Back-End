<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
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
            'articlesOfIncorporation' => [
                'sometimes',
                'file',
                'mimes:jpeg,png,jpg,pdf',
                'max:10240',
            ],

            'govemorLicense' => [
                'sometimes',
                'file',
                'mimes:jpeg,png,jpg,pdf',
                'max:10240',
            ],

            'partnersIDCards' => [
                'sometimes',
                'file',
                'mimes:jpeg,png,jpg,pdf',
                'max:10240',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'articlesOfIncorporation.file'     => 'يجب أن يكون عقد التأسيس ملفا صالحا.',
            'articlesOfIncorporation.mimes'    => 'يجب أن يكون عقد التأسيس من نوع: jpeg, png, jpg أو pdf.',
            'articlesOfIncorporation.max'      => 'يجب ألا يتجاوز حجم عقد التأسيس 10 MB.',

            'govemorLicense.file'     => 'يجب أن يكون ترخيص المحافظ ملفًا.',
            'govemorLicense.mimes'    => 'يجب أن يكون ترخيص المحافظ من نوع: jpeg, png, jpg أو pdf.',
            'govemorLicense.max'      => 'يجب ألا يتجاوز حجم ترخيص المحافظ 10 MB.',

            'partnersIDCards.file'     => 'يجب أن تكون بطائق الشركاء ملفات.',
            'partnersIDCards.mimes'    => 'يجب أن تكون بطائق الشركاء من نوع: jpeg, png, jpg أو pdf.',
            'partnersIDCards.max'      => 'يجب ألا يتجاوز حجم بطائق الشركاء 10 MB.',
        ];
    }
}
