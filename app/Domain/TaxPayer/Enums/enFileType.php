<?php

namespace App\Domain\TaxPayer\Enums;

enum enFileType: string
{
    case Individual = 'Individual';
    case Company = 'Company';
    case CharitableCompany = 'CharitableCompany';

    public function label(): string
    {
        return match($this) {
            self::Individual => 'ملف فرد',
            self::Company => 'ملف شركة',
            self::CharitableCompany => 'ملف شركة خيرية',
        };
    }
}
