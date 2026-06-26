<?php

namespace App\Application\PaymentType\UseCases;

use App\Domain\PaymentType\Repositories\PaymentTypeRepositoryInterface;
use DomainException;

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
            throw new DomainException("لا يوجد نوع دفع مع ال ID [$id].");
        }

        return $paymentType;
    }
}
