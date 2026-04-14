<?php

namespace App\Application\PaymentType\UseCases;

use App\Domain\PaymentType\Repositories\PaymentTypeRepositoryInterface;

class ShowPaymentTypeUseCase
{
    public function __construct(
        private PaymentTypeRepositoryInterface $payment_type_repository_interface
    )
    {}

    public function execute($id)
    {
        $paymentType = $this->payment_type_repository_interface->findById($id);

        if(!$paymentType)
        {
            throw new \Exception("Payment Type with ID [$id] not found.");
        }

        return $paymentType;
    }
}
