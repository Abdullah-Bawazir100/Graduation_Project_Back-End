<?php

namespace App\Application\RecyclePin\UseCases;

use App\Domain\RecyclePin\Repositories\RecyclePinRepositoryInterface;
use Exception;

class DeleteRecyclePinUseCase
{
    public function __construct(
        private RecyclePinRepositoryInterface $repository
    ) {}

    public function execute(int $id): bool
    {
        $recyclePin = $this->repository->findById($id);

        if (!$recyclePin) {
            throw new Exception("لا يوجد سجل في سلة المحذوفات مع ال ID [$id].");
        }

        $this->repository->delete($id);

        return true;
    }
}
