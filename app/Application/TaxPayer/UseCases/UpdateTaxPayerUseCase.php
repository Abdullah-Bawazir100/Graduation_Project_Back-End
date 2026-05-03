<?php

namespace  App\Application\TaxPayer\UseCases;

use App\Application\TaxPayer\DTOs\TaxPayerDTOs;
use App\Application\User\DTOs\UserDTO;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\Interfaces\PasswordHashInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class UpdateTaxPayerUseCase
{
    public function __construct(
        private TaxPayerRepositoryInterface $tax_payer_repository,
    )
    {}

    public function execute(TaxPayerDTOs $taxPayerDTO , int $id)
    {
        $existingTaxPayer = $this->tax_payer_repository->findById($id);
        if(!$existingTaxPayer)
        {
            throw new DomainException("المكلف مع ال ID [{$id}] غير موجود.");
        }

        $taxPayer = new TaxPayer(
            id: $id,
            userId: $taxPayerDTO->userId,
            commercialRecord: $taxPayerDTO->commercialRecord,
            activityLicense: $taxPayerDTO->activityLicense,
            tradePict: $taxPayerDTO->tradePict,
            insuranceCard: $taxPayerDTO->insuranceCard,
            propertyDocPict: $taxPayerDTO->propertyDocPict,
            fileType: $taxPayerDTO->fileType,
        );
        $updatedTaxPayer = $this->tax_payer_repository->update($taxPayer, $id);
        return $updatedTaxPayer;
    }
}
