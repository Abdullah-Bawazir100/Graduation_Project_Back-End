<?php

namespace App\Application\TaxPayer\UseCases;

use App\Application\TaxPayer\DTOs\TaxPayerDTOs;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class CreateFileToExistingTaxPayerUseCase
{
    public function __construct(
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute(TaxPayerDTOs $taxPayerDTO, int $userId)
    {
        $existingUser = $this->user_repository->findById($userId);

        if($existingUser->role !== UserRole::Tax_Payer)
        {
            throw new DomainException("المستخدم الموجود مع ال ID [$userId] ليس مكلف.");
        }

        $newTaxPayerFile = new TaxPayer(
            id: null,
            userId: $existingUser->id,
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
            $existingUser->id
        );

        if (!$createdTaxPayerFile) {
            throw new DomainException("فشل إنشاء الملف الجديد للمكلف.");
        }

        return [
            'taxPayerInfo' => $createdTaxPayerFile,
            'userInfo' => $existingUser->toArray()
        ];
    }
}
