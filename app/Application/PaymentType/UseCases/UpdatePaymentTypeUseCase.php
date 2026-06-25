<?php

namespace App\Application\PaymentType\UseCases;

use App\Application\PaymentType\DTOs\PaymentTypeDTOs;
use App\Domain\PaymentType\Entities\PaymentType;
use App\Domain\PaymentType\Repositories\PaymentTypeRepositoryInterface;
use DomainException;

class UpdatePaymentTypeUseCase
{
    public function __construct(
        private PaymentTypeRepositoryInterface $payment_type_repository_interface
    )
    {}

    public function execute(int $id , PaymentTypeDTOs $paymentTypeDTOs)
    {
        $paymentType = $this->payment_type_repository_interface->findById($id);
        if(!$paymentType)
        {
            throw new DomainException("نوع الدفع مع ال ID [$id] غير موجود.");
        }

        $name = trim($paymentTypeDTOs->name);
        $note = trim($paymentTypeDTOs->note);

        return $this->payment_type_repository_interface->update(
            new PaymentType($id , $name , $note)
        );

    }
}
