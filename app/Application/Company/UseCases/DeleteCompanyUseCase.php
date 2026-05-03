<?php

namespace App\Application\Company\UseCases;

use App\Domain\Company\Repositories\CompanyRepositoryInterface;

class DeleteCompanyUseCase
{
    public function __construct(
        private CompanyRepositoryInterface  $company_repository,
    ) {}

    public function execute(int $id): void
    {
        $company = $this->company_repository->findById($id);
        if(!$company)
        {
            throw new \DomainException("لا يوجد ملف شركة مع ال ID [{$id}].");
        }
        
        $this->company_repository->delete($id);
    }
}
