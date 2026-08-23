<?php

namespace App\Application\CharitableCompany\UseCases;

use App\Application\CharitableCompany\DTOs\CharitableCompanyDTOs;
use App\Domain\CharitableCompany\Entities\CharitableCompany;
use App\Domain\CharitableCompany\Repositories\CharitableCompanyRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\File\Repositories\FileRepositoryInterface;
use DomainException;

class UpdateCharitableCompanyUseCase
{
    public function __construct(
        private CharitableCompanyRepositoryInterface $charitable_company_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository,
        private FileRepositoryInterface $file_repository
    )
    {}

    public function execute(CharitableCompanyDTOs $charitableCompanyDTOs , int $id , ?int $authenticatedUserId = null)
    {
        $existingCharitableCompany = $this->charitable_company_repository->findById($id);
        if(!$existingCharitableCompany)
        {
            throw new DomainException("لا يوجد ملف شركة خيرية مع ال ID [{$id}].");
        }
        $taxPayer = $this->tax_payer_repository->findById($existingCharitableCompany->taxPayerId);

        $file = $this->file_repository->findById($taxPayer->fileId);
        if (!$file) {
            throw new DomainException("عذراً، لم يتم العثور على ملف لهذه الشركة الخيرية.");
        }
        $existingUser = $file->user;
        $charitableCompany = new CharitableCompany(
            id: $id,
            taxPayerId: $taxPayer->id,
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
            //'fileInfo' => $file,
            'taxPayerInfo' => $taxPayer,
        ];
    }
}
