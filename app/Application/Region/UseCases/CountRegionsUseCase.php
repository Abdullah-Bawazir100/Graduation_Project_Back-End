<?php

namespace App\Application\Region\UseCases;

use App\Domain\Region\Repositories\RegionRepositoryInterface;

class CountRegionsUseCase
{
    public function __construct(
        private RegionRepositoryInterface $repository
    ) {}

    public function execute(): int
    {
        return $this->repository->countRegions();
    }
}