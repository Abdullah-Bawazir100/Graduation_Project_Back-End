<?php

namespace App\Application\PaymentType\UseCases;

use App\Domain\PaymentType\Repositories\PaymentTypeRepositoryInterface;

class DeletePaymentTypeUseCase
{
    public function __construct(
        private PaymentTypeRepositoryInterface $payment_type_repository_interface
    )
    {}

    public function execute(int $id)
    {
        $paymentType = $this->payment_type_repository_interface->findById($id);

        if (!$paymentType) {
            throw new \DomainException("نوع الدفع مع ال ID [$id] غير موجود.");
        }

        $this->payment_type_repository_interface->delete($id);
    }
}
