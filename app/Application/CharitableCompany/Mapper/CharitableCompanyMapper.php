<?php

namespace App\Application\CharitableCompany\Mapper;

use App\Domain\CharitableCompany\Entities\CharitableCompany;

class CharitableCompanyMapper
{
    public static function toArray(CharitableCompany $charitableCompany): array
    {
        return [
            'id' => $charitableCompany->id,
            'tax_payer_id' => $charitableCompany->tax_payer_id,
            'byLawsCopy' => $charitableCompany->byLawsCopy
        ];
    }
}
