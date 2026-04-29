<?php

namespace App\Application\PaymentType\UseCases;

use App\Domain\PaymentType\Repositories\PaymentTypeRepositoryInterface;

class CountPaymentsTypesUseCase
{
    public function __construct(
        private PaymentTypeRepositoryInterface $repository
    ) {}

    public function execute(): int
    {
        return $this->repository->countPaymentsTypes();
    }
}