<?php

namespace App\Application\CharitableCompany\UseCases;

use App\Domain\CharitableCompany\Repositories\CharitableCompanyRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;

class FindCharitableCompanyByIdUseCase
{
    public function __construct(
        private CharitableCompanyRepositoryInterface $charitable_company_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute(int $id)
    {
        $charitableCompany = $this->charitable_company_repository->findById($id);
        if(!$charitableCompany)
        {
            throw new \DomainException("ملف الشركة الخيرية مع ال ID [{$id}] غير موجود.");
        }

        $taxPayer = null;
        $taxPayerUserInfo = null;

        if ($charitableCompany->tax_payer_id) {
            $taxPayer = $this->tax_payer_repository->findById($charitableCompany->tax_payer_id);

            if ($taxPayer && $taxPayer->userId) {
                $user = $this->user_repository->findById($taxPayer->userId);
                if ($user) {
                    $taxPayerUserInfo = [
                        'id' => $user->id,
                        'fullName' => $user->firstName . ' ' . $user->lastName,
                        'userName' => $user->userName,
                        'phone' => $user->phone,
                        'fileType' => $taxPayer->fileType,
                    ];
                }
            }
        }

        return [
            'charitableCompany' => $charitableCompany,
            'taxPayerInfo' => $taxPayerUserInfo
        ];
    }
}
