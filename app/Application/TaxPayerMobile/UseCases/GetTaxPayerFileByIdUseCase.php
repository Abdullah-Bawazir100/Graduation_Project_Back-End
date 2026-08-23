<?php

namespace App\Application\TaxPayerMobile\UseCases;

use App\Application\TaxPayerMobile\Mapper\TaxPayerInfoMapper;
use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Domain\TaxInformation\Repositories\TaxInformationRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\TaxPayerMobile\Repositories\TaxPayerMobileRepositoryInterface;
use DomainException;

class GetTaxPayerFileByIdUseCase
{
    public function __construct(
        private TaxPayerMobileRepositoryInterface $tax_payer_mobile_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private TaxInformationRepositoryInterface $tax_information_repository,
        private FileRepositoryInterface $file_repository
    )
    {
    }

    public function execute(int $taxPayerId , int $authenticatedUserId)
    {
        $taxPayerAsUser = $this->tax_payer_repository->findByUserId($authenticatedUserId);
        if(!$taxPayerAsUser)
        {
            throw new DomainException("لا يوجد مستخدم مكلف مع ال ID [$authenticatedUserId].");
        }

        $taxPayer = $this->tax_payer_repository->findById($taxPayerId);
        if(!$taxPayer)
        {
            throw new DomainException("لا يوجد مكلف مع ال ID [$taxPayerId].");
        }

        if($taxPayerAsUser->userId !== $taxPayer->userId)
        {
            throw new DomainException("غير مصرح لك بعرض ملف مكلف اخر.");
        }

        $taxInfo = $this->tax_information_repository->getTaxInformationByTaxPayerId($taxPayer->id);
        $taxPayerFile = $this->tax_payer_mobile_repository->getTaxPayerFileById($taxPayerId);
        $file = $this->file_repository->getFileByUserId($taxPayer->userId);
        return [
            TaxPayerInfoMapper::map(
                $taxPayerFile,
                $taxInfo,
                $file
            )
        ];
    }
}
