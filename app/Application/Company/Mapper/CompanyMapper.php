<?php

namespace App\Application\Company\Mapper;

use App\Domain\Company\Entities\Company;

class CompanyMapper
{
    public static function toArray(Company $company): array
    {
        return [
            'id' => $company->id,
            'tax_payer_id' => $company->tax_payer_id,
            'articlesOfIncorporation' => $company->articlesOfIncorporation,
            'govemorLicense' => $company->govemorLicense,
            'partnersIDCards' => $company->partnersIDCards,
        ];
    }
}
