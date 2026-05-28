<?php

namespace  App\Application\TaxPayer\UseCases;

use App\Application\TaxPayer\DTOs\TaxPayerDTOs;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class UpdateTaxPayerUseCase
{
    public function __construct(
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository,
    )
    {}

    public function execute(TaxPayerDTOs $taxPayerDTO , int $id , int $authenticatedUserId)
    {
        $actor = $this->user_repository->findById($authenticatedUserId);
        $isAdmin = $actor->role === UserRole::Admin;

        $existingTaxPayer = $this->tax_payer_repository->findById($id);
        $existingUser = $this->user_repository->findById($taxPayerDTO->userId);
        if(!$existingTaxPayer)
        {
            throw new DomainException("المكلف مع ال ID [{$id}] غير موجود.");
        }

        if (!$isAdmin) {
            $actorDeptId = (int)$actor->department->id;
            $oldUser = $this->user_repository->findById($existingTaxPayer->userId);

            if ($oldUser && $actorDeptId !== (int)$oldUser->department->id) {
                throw new DomainException('غير مصرح لك بتحديث بيانات مكلف من قسم غير القسم الذي تعمل فيه.');
            }

            if ($existingUser && $actorDeptId !== (int)$existingUser->department->id) {
                throw new DomainException('غير مصرح لك بنقل المكلف إلى قسم غير القسم الذي تعمل فيه.');
            }
        }


        $taxPayer = new TaxPayer(
            id: $id,
            userId: $taxPayerDTO->userId,
            tradeName: $taxPayerDTO->tradeName,
            commercialRecord: $taxPayerDTO->commercialRecord,
            activityLicense: $taxPayerDTO->activityLicense,
            tradePict: $taxPayerDTO->tradePict,
            insuranceCard: $taxPayerDTO->insuranceCard,
            propertyDocPict: $taxPayerDTO->propertyDocPict,
            fileType: $taxPayerDTO->fileType,
            source: $taxPayerDTO->source
        );
        $updatedTaxPayer = $this->tax_payer_repository->update($taxPayer, $id);
        return [
            'TaxPayerInfo' => $updatedTaxPayer,
            'userInfo' => $existingUser,
        ];
    }
}
