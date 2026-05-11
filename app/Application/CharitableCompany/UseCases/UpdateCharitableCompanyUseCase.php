<?php

namespace App\Application\CharitableCompany\UseCases;

use App\Application\CharitableCompany\DTOs\CharitableCompanyDTOs;
use App\Domain\CharitableCompany\Entities\CharitableCompany;
use App\Domain\CharitableCompany\Repositories\CharitableCompanyRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class UpdateCharitableCompanyUseCase
{
    public function __construct(
        private CharitableCompanyRepositoryInterface $charitable_company_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute(CharitableCompanyDTOs $charitableCompanyDTOs , int $id)
    {
        $existingCharitableCompany = $this->charitable_company_repository->findById($id);
        if(!$existingCharitableCompany)
        {
            throw new DomainException("لا يوجد ملف شركة خيرية مع ال ID [{$id}].");
        }
        $taxPayer = $this->tax_payer_repository->findById($existingCharitableCompany->tax_payer_id);
        $user = $this->user_repository->findById($taxPayer->userId);
        $charitableCompany = new CharitableCompany(
            id: $id,
            tax_payer_id: $taxPayer->id,
            byLawsCopy: $charitableCompanyDTOs->byLawsCopy,
        );

        $updatedCharitableCompany = $this->charitable_company_repository->update($charitableCompany , $id);

        return [
            'charitableCompanyInfo' => $updatedCharitableCompany,
            'taxPayerInfo' => $taxPayer,
            'userInfo' => $user->toArray(),
        ];
    }
}
