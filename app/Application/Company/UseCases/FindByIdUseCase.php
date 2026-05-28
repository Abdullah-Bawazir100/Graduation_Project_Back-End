<?php

namespace App\Application\Company\UseCases;

use App\Domain\Company\Repositories\CompanyRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class FindByIdUseCase
{
    public function __construct(
        private CompanyRepositoryInterface $company_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute(int $id, ?int $authenticatedUserId = null)
    {
        $company = $this->company_repository->findById($id);
        if(!$company)
        {
            throw new DomainException("ملف الشركة مع ال ID [{$id}] غير موجود.");
        }

        $taxPayer = null;
        $taxPayerUserInfo = null;

        if ($company->tax_payer_id) {
            $taxPayer = $this->tax_payer_repository->findById($company->tax_payer_id);

            if ($taxPayer && $taxPayer->userId) {
                $user = $this->user_repository->findById($taxPayer->userId);
                if ($user) {
                    $taxPayerUserInfo = $user;
                }
            }
        }

        if ($authenticatedUserId !== null && $taxPayerUserInfo) {
            $actor = $this->user_repository->findById($authenticatedUserId);
            if ($actor && $actor->role !== UserRole::Admin) {
                if ((int)$actor->department->id !== (int)$taxPayerUserInfo->department->id) {
                    throw new DomainException('غير مصرح لك بعرض بيانات شركة من قسم غير القسم الذي تعمل فيه.');
                }
            }
        }

        return [
            'companyInfo' => $company,
            'taxPayerInfo' => $taxPayer,
            'userInfo' => $taxPayerUserInfo->toArray()
        ];
    }
}
