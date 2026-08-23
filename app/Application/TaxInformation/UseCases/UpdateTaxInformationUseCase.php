<?php

namespace App\Application\TaxInformation\UseCases;

use App\Application\TaxInformation\DTOs\TaxInformationDTOs;
use App\Domain\File\Repositories\FileRepositoryInterface;
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
        private FileRepositoryInterface $file_repository,
    )
    {}

    public function execute(int $id , TaxInformationDTOs $dto)
    {
        $existingTaxInfo = $this->tax_information_repository->findById($id);
        if (!$existingTaxInfo) {
            throw new DomainException("لا توجد معلومات ضريبية مع الـ ID [$id].");
        }

        $existingTaxType = $this->tax_type_repository->findById($dto->taxTypeId);
        if(!$existingTaxType)
        {
            throw new DomainException("لا يوجد نوع ضريبة مع الـ ID [$dto->taxTypeId].");
        }

        $existingFile = $this->file_repository->findById($dto->fileId);
        if(!$existingFile)
        {
            throw new DomainException("لا يوجد ملف مع الـ ID [$dto->fileId].");
        }


        $updatedTaxInfo = new TaxInformation(
            id: $id,
            taxTypeId: $dto->taxTypeId ??  $existingTaxType->id,
            fileId: $dto->fileId ?? $existingFile->id,
            taxAmount: $dto->taxAmount,
            lastPayment: $dto->lastPayment,
            attachment: $dto->attachment,
            taxType:  $existingTaxType,
            file: $existingFile,
        );
        return $this->tax_information_repository->update($updatedTaxInfo);
    }
}
