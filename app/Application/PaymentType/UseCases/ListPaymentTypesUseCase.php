<?php

namespace App\Application\PaymentType\UseCases;

use App\Domain\PaymentType\Repositories\PaymentTypeRepositoryInterface;

class ListPaymentTypesUseCase
{
    public function __construct(
        private PaymentTypeRepositoryInterface $payment_type_repository_interface
    )
    {}

    public function execute()
    {
        return $this->payment_type_repository_interface->getAll();
    }
}
