<?php

namespace App\Application\CharitableCompany\UseCases;

use App\Domain\CharitableCompany\Repositories\CharitableCompanyRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class FindCharitableCompanyByIdUseCase
{
    public function __construct(
        private CharitableCompanyRepositoryInterface $charitable_company_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository
    ) {}

    public function execute(int $id , ?int $authenticatedUserId)
    {
        $charitableCompany = $this->charitable_company_repository->findById($id);
        if (!$charitableCompany) {
            throw new DomainException("ملف الشركة الخيرية مع ال ID [{$id}] غير موجود.");
        }

        $taxPayer = null;
        $taxPayerUserInfo = null;

        if ($charitableCompany->tax_payer_id) {
            $taxPayer = $this->tax_payer_repository->findById($charitableCompany->tax_payer_id);

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
                    throw new DomainException('غير مصرح لك بعرض بيانات شركة خيرية من قسم غير القسم الذي تعمل فيه.');
                }
            }
        }

        return [
            'charitableCompanyInfo' => $charitableCompany,
            'taxPayerInfo' => $taxPayer,
            'userInfo' => $taxPayerUserInfo
        ];
    }
}
