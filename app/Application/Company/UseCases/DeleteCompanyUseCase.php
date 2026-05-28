<?php

namespace App\Application\Company\UseCases;

use App\Domain\Company\Repositories\CompanyRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class DeleteCompanyUseCase
{
    public function __construct(
        private CompanyRepositoryInterface  $company_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository
    ) {}

    public function execute(int $id, ?int $authenticatedUserId = null): void
    {
        $company = $this->company_repository->findById($id);
        if(!$company)
        {
            throw new DomainException("لا يوجد ملف شركة مع ال ID [{$id}].");
        }
        $taxPayer = $this->tax_payer_repository->findById($company->tax_payer_id);

        // التحقق من صلاحيات القسم
        if ($authenticatedUserId !== null && $taxPayer) {
            $user = $this->user_repository->findById($taxPayer->userId);
            $actor = $this->user_repository->findById($authenticatedUserId);
            if ($actor && $actor->role !== UserRole::Admin) {
                if ((int)$actor->department->id !== (int)$user->department->id) {
                    throw new DomainException('غير مصرح لك بحذف شركة من قسم غير القسم الذي تعمل فيه.');
                }
            }
        }

        $this->company_repository->delete($id);
        $this->tax_payer_repository->delete($taxPayer->id);
    }
}
