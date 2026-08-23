<?php

namespace App\Application\TaxPayer\UseCases;

use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use App\Domain\File\Repositories\FileRepositoryInterface;
use DomainException;

class FindTaxPayerByIdUseCase
{
    public function __construct(
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository,
        private FileRepositoryInterface $file_repository
    )
    {}

    public function execute(int $id, ?int $authenticatedUserId = null)
    {
        $taxPayer = $this->tax_payer_repository->findById($id);
        if(!$taxPayer)
        {
            return null;
        }

        if ($authenticatedUserId !== null) {
            $actor = $this->user_repository->findById($authenticatedUserId);
            if ($actor && $actor->role !== UserRole::Admin) {
                $actorDeptId = (int)$actor->department->id;
                $file = $taxPayer->fileId ? $this->file_repository->findById($taxPayer->fileId) : null;
                $oldUser = $file ? $file->user : null;
                if ($oldUser && $actorDeptId !== (int)$oldUser->department->id) {
                    throw new DomainException('غير مصرح لك بعرض بيانات مكلف من قسم غير القسم الذي تعمل فيه.');
                }
            }
        }

        return $taxPayer;
    }
}
