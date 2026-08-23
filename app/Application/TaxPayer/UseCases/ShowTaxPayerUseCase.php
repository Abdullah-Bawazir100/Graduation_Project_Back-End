<?php

namespace App\Application\TaxPayer\UseCases;

use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\File\Repositories\FileRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\Auth;

class ShowTaxPayerUseCase
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

        if  (!$taxPayer) {
            throw new DomainException("المكلف مع ال ID [{$id}] غير موجود.");
        }

        $file = null;
        if ($taxPayer->fileId) {
            $file = $this->file_repository->findById($taxPayer->fileId);
        }

        if ($authenticatedUserId !== null) {
            $actor = $this->user_repository->findById($authenticatedUserId);
            if ($actor && $actor->role !== UserRole::Admin) {
                $actorDeptId = (int)$actor->department->id;
                $oldUser = $file ? $file->user : null;
                if ($oldUser && $actorDeptId !== (int)$oldUser->department->id) {
                    throw new DomainException('غير مصرح لك بعرض بيانات مكلف من قسم غير القسم الذي تعمل فيه.');
                }
            }
        }

        $userInfo = null;

        if ($file && $file->user) {
            $userInfo = $file->user->toArray();
        }

        return [
            'taxPayerInfo' => $taxPayer,
            'userInfo' => $userInfo
        ];
    }
}
