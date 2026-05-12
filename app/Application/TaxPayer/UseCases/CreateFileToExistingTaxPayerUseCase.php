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
        // Find the existing taxpayer by ID to get the user ID
        $existingTaxPayer = $this->tax_payer_repository->findById($taxPayerId);
        if (!$existingTaxPayer) {
            throw new DomainException("المكلف مع الـ ID [$taxPayerId] غير موجود.");
        }

        // Create a new taxpayer file with the same user ID as the existing taxpayer
        $newTaxPayerFile = new TaxPayer(
            id: null, // New record, so ID should be null
            userId: $existingTaxPayer->userId, // Same user ID as the existing taxpayer
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
            'taxPayerInfo' => $createdTaxPayerFile
        ];
    }
}
