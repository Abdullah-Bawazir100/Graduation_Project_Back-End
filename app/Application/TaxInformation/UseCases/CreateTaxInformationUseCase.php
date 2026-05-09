<?php

namespace App\Application\TaxInformation\UseCases;

use App\Application\TaxInformation\DTOs\TaxInformationDTO;
use App\Application\TaxInformation\DTOs\TaxInformationDTOs;
use App\Domain\TaxInformation\Entities\TaxInformation;
use App\Domain\TaxInformation\Repositories\TaxInformationRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\TaxPayerMobile\Repositories\TaxPayerMobileRepositoryInterface;
use App\Domain\TaxType\Repositories\TaxTypeRepositoryInterface;
use DomainException;

class CreateTaxInformationUseCase
{
    public function __construct(
        private TaxInformationRepositoryInterface $tax_information_repository,
        private TaxTypeRepositoryInterface $tax_type_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository
    )
    {}

    public function execute(TaxInformationDTOs $taxInformationDTOs)
    {
        $taxType = $this->tax_type_repository->findById($taxInformationDTOs->taxTypeId);
        if(!$taxType)
        {
            throw new DomainException("لا يوجد نوع ضريبة مع ال ID [{$taxInformationDTOs->taxTypeId}].");
        }

        $taxPayer = $this->tax_payer_repository->findById($taxInformationDTOs->taxPayerId);
        if(!$taxType)
        {
            throw new DomainException("لا يوجد نوع ضريبة مع ال ID [{$taxInformationDTOs->taxPayerId}].");
        }

        $taxInformation = new TaxInformation(
            id: null,
            taxTypeId: $taxType->id,
            taxPayerId: $taxPayer->id,
            taxAmount: $taxInformationDTOs->taxAmount,
            lastPayment: $taxInformationDTOs->lastPayment,
            taxType: $taxType,
            taxPayer: $taxPayer
        );

        $createdTaxInfo = $this->tax_information_repository->create($taxInformation);
        return [
            'taxInformation' => $createdTaxInfo,
        ];
    }
}
