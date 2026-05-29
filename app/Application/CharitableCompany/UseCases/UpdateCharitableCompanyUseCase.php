<?php

namespace App\Application\CharitableCompany\UseCases;

use App\Application\CharitableCompany\DTOs\CharitableCompanyDTOs;
use App\Domain\CharitableCompany\Entities\CharitableCompany;
use App\Domain\CharitableCompany\Repositories\CharitableCompanyRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Enums\UserRole;
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

    public function execute(CharitableCompanyDTOs $charitableCompanyDTOs , int $id , ?int $authenticatedUserId = null)
    {
        $existingCharitableCompany = $this->charitable_company_repository->findById($id);
        if(!$existingCharitableCompany)
        {
            throw new DomainException("لا يوجد ملف شركة خيرية مع ال ID [{$id}].");
        }
        $taxPayer = $this->tax_payer_repository->findById($existingCharitableCompany->tax_payer_id);
        $existingUser = $this->user_repository->findById($taxPayer->userId);
        $charitableCompany = new CharitableCompany(
            id: $id,
            tax_payer_id: $taxPayer->id,
            byLawsCopy: $charitableCompanyDTOs->byLawsCopy,
        );

        if ($authenticatedUserId !== null) {
            $actor = $this->user_repository->findById($authenticatedUserId);
            if ($actor && $actor->role !== UserRole::Admin) {
                if ((int)$actor->department->id !== (int)$existingUser->department->id) {
                    throw new DomainException('غير مصرح لك بتحديث بيانات شركة خيرية من قسم غير القسم الذي تعمل فيه.');
                }
            }
        }

        $updatedCharitableCompany = $this->charitable_company_repository->update($charitableCompany , $id);

        return [
            'charitableCompanyInfo' => $updatedCharitableCompany,
            'taxPayerInfo' => $taxPayer,
            'userInfo' => $existingUser->toArray(),
        ];
    }
}
