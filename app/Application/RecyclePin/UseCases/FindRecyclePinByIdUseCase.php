<?php

namespace App\Application\RecyclePin\UseCases;

use App\Domain\RecyclePin\Repositories\RecyclePinRepositoryInterface;
use Exception;

class FindRecyclePinByIdUseCase
{

    public function __construct(
        private RecyclePinRepositoryInterface $repository
    ) {}

    public function execute(int $id)
    {
        $recyclePin = $this->repository->findById($id);

        if (!$recyclePin) {
            throw new Exception("لا يوجد سجل في سلة المحذوفات مع ال ID [$id].");
        }

        return $recyclePin;
    }
}
