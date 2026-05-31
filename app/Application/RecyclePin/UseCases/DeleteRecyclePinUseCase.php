<?php

namespace App\Application\RecyclePin\UseCases;

use App\Domain\RecyclePin\Repositories\RecyclePinRepositoryInterface;
use Exception;

class DeleteRecyclePinUseCase
{
    private RecyclePinRepositoryInterface $repository;

    public function __construct(RecyclePinRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): bool
    {
        $recyclePin = $this->repository->findById($id);

        if (!$recyclePin) {
            throw new Exception("Recycle pin record not found.");
        }

        $this->repository->delete($id);

        return true;
    }
}
