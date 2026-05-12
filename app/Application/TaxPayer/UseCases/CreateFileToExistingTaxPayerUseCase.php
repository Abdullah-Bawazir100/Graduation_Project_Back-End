<?php

namespace App\Application\TaxPayer\UseCases;

use App\Application\TaxPayer\DTOs\TaxPayerDTOs;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class CreateFileToExistingTaxPayerUseCase
{
    public function __construct(
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute(TaxPayerDTOs $taxPayerDTO, int $taxPayerId)
    {
        $existingTaxPayer = $this->tax_payer_repository->findById($taxPayerId);
        if (!$existingTaxPayer) {
            throw new DomainException("المكلف مع الـ ID [$taxPayerId] غير موجود.");
        }
        $user = $this->user_repository->findById($existingTaxPayer->userId);

        $newTaxPayerFile = new TaxPayer(
            id: null,
            userId: $existingTaxPayer->userId,
            tradeName: $taxPayerDTO->tradeName,
            commercialRecord: $taxPayerDTO->commercialRecord,
            activityLicense: $taxPayerDTO->activityLicense,
            tradePict: $taxPayerDTO->tradePict,
            insuranceCard: $taxPayerDTO->insuranceCard,
            propertyDocPict: $taxPayerDTO->propertyDocPict,
            fileType: $taxPayerDTO->getFileType(),
        );

        $createdTaxPayerFile = $this->tax_payer_repository->createFileToExistingTaxPayer(
            $newTaxPayerFile,
            $existingTaxPayer->userId // Pass the user ID instead of taxpayer ID
        );

        if (!$createdTaxPayerFile) {
            throw new DomainException("فشل إنشاء الملف الجديد للمكلف.");
        }

        return [
            'taxPayerInfo' => $createdTaxPayerFile,
            'userInfo' => $user->toArray()
        ];
    }
}
