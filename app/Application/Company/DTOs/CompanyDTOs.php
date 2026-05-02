<?php

namespace App\Application\Company\DTOs;

use App\Domain\TaxPayer\Entities\TaxPayer;

class CompanyDTOs
{
    public function __construct(
        public ?string $articlesOfIncorporation,
        public ?string $govemorLicense,
        public ?string $partnersIDCards,
    )
    {}
}
