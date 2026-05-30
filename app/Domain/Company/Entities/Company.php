<?php

namespace App\Domain\Company\Entities;

class Company
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $tax_payer_id,
        public ?string $articlesOfIncorporation,
        public ?string $govemorLicense,
        public ?string $partnersIDCards,
    ) {}
}
