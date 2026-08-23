<?php

namespace App\Application\TaxPayer\UseCases;

use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\File\Repositories\FileRepositoryInterface;

class ListTaxPayersUseCase
{
    public function __construct(
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository,
        private FileRepositoryInterface $file_repository
    )
    {}

    public function execute(int $authenticatedUserId)
    {
        $actor = $this->user_repository->findById($authenticatedUserId);
        $isAdmin = $actor->role === UserRole::Admin;
        $departmentId = $isAdmin ? null : (int)$actor->department->id;

        $taxPayers = $this->tax_payer_repository->getAll($departmentId);

        $result = [];
        foreach ($taxPayers as $taxPayer) {
            $userInfo = null;

            if ($taxPayer->fileId) {
                $file = $this->file_repository->findById($taxPayer->fileId);
                if ($file && $file->user) {
                    $userInfo = $file->user->toArray(); // Return the full user object/array instead of selected fields
                }
            }

            $result[] = [
                'taxPayerInfo' => $taxPayer,
                'userInfo' => $userInfo
            ];
        }

        return [
            'taxPayers' => $result
        ];
    }
}
