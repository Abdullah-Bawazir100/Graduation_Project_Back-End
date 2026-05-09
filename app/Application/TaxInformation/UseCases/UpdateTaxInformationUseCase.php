<?php

namespace App\Application\TaxInformation\UseCases;

use App\Application\TaxInformation\DTOs\TaxInformationDTOs;
use App\Domain\TaxInformation\Entities\TaxInformation;
use App\Domain\TaxInformation\Repositories\TaxInformationRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\TaxType\Repositories\TaxTypeRepositoryInterface;
use DomainException;

class UpdateTaxInformationUseCase
{
    public function __construct(
        private TaxInformationRepositoryInterface $tax_information_repository,
        private TaxTypeRepositoryInterface $tax_type_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository
    )
    {}

    public function execute(int $id , TaxInformationDTOs $dto)
    {
        $existingTaxType = $this->tax_type_repository->findById($dto->taxTypeId);
        if(!$existingTaxType)
        {
            throw new DomainException("لا يوجد نوع ضريبة مع ال ID [$dto->taxTypeId].");
        }

        $existingTaxPayer = $this->tax_payer_repository->findById($dto->taxPayerId);
        if(!$existingTaxPayer)
        {
            throw new DomainException("لا يوجد مكلف ضريبة مع ال ID [$dto->taxPayerId].");
        }

        $taxPayer = $this->tax_payer_repository->findById($dto->taxPayerId);

        $updatedTaxInfo = new TaxInformation(
            id: $id,
            taxTypeId: $dto->taxTypeId ??  $existingTaxType->id,
            taxPayerId: $dto->taxPayerId ?? $taxPayer->id,
            taxAmount: $dto->taxAmount,
            lastPayment: $dto->lastPayment,
            taxType: $existingTaxType,
            taxPayer: $taxPayer,
        );
        return $this->tax_information_repository->update($updatedTaxInfo);
    }
}
