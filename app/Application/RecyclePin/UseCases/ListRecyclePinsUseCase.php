<?php

namespace App\Application\RecyclePin\UseCases;

use App\Domain\RecyclePin\Repositories\RecyclePinRepositoryInterface;

class ListRecyclePinsUseCase
{
    private RecyclePinRepositoryInterface $repository;

    public function __construct(RecyclePinRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(): array
    {
        return $this->repository->getAll();
    }
}
