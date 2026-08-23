<?php

namespace App\Application\TaxInformation\UseCases;

use App\Application\TaxInformation\DTOs\TaxInformationDTO;
use App\Application\TaxInformation\DTOs\TaxInformationDTOs;
use App\Domain\File\Repositories\FileRepositoryInterface;
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
        private FileRepositoryInterface $file_repository
    )
    {}

    public function execute(TaxInformationDTOs $taxInformationDTOs)
    {
        $taxType = $this->tax_type_repository->findById($taxInformationDTOs->taxTypeId);
        if(!$taxType)
        {
            throw new DomainException("لا يوجد نوع ضريبة مع ال ID [{$taxInformationDTOs->taxTypeId}].");
        }

        $file = $this->file_repository->findById($taxInformationDTOs->fileId);
        if(!$file)
        {
            throw new DomainException("لا يوجد ملف مع ال ID [{$taxInformationDTOs->fileId}].");
        }

        $existingTaxInfo = $this->tax_information_repository->getTaxInformationByFileId($file->id);
        if (!empty($existingTaxInfo)) {
            throw new DomainException("عذراً، هذا الملف يمتلك معلومات ضريبية مسبقاً. لا يمكنك إضافة معلومات ضريبية جديدة لنفس الملف.");
        }

        $taxInformation = new TaxInformation(
            id: null,
            taxTypeId: $taxType->id,
            fileId: $file->id,
            taxAmount: $taxInformationDTOs->taxAmount,
            lastPayment: $taxInformationDTOs->lastPayment,
            attachment: $taxInformationDTOs->attachment,
            taxType: $taxType,
            file: $file
        );

        $createdTaxInfo = $this->tax_information_repository->create($taxInformation);
        return [
            'taxInformation' => $createdTaxInfo,
        ];
    }
}
