<?php

namespace App\Application\CharitableCompany\UseCases;

use App\Domain\CharitableCompany\Repositories\CharitableCompanyRepositoryInterface;
use DomainException;

class DeleteCharitableCompanyUseCase
{
    public function __construct(
        private CharitableCompanyRepositoryInterface $charitable_company_repository
    )
    {}

    public function execute(int $id): void
    {
        $charitableCompany = $this->charitable_company_repository->findById($id);
        if(!$charitableCompany)
        {
            throw new DomainException("لا يوجد ملف شركة خيرية مع ال ID [{$id}].");
        }
        $this->charitable_company_repository->delete($id);
    }
}
