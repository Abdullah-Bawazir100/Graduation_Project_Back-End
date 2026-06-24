<?php

namespace App\Application\District\UseCases;

use App\Domain\District\Repositories\DistrictRepositoryInterface;

class CountDistrictsUseCase
{
    public function __construct(
        private DistrictRepositoryInterface $repository
    ) {}

    public function execute(): int
    {                echo "hhh";

        return $this->repository->countDistricts();
    }
}
