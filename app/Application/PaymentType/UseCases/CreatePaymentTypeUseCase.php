<?php

namespace App\Application\PaymentType\UseCases;

use App\Application\PaymentType\DTOs\PaymentTypeDTOs;
use App\Domain\PaymentType\Entities\PaymentType;
use App\Domain\PaymentType\Repositories\PaymentTypeRepositoryInterface;

class CreatePaymentTypeUseCase
{
    public function __construct(
        private PaymentTypeRepositoryInterface $paymentTypeRepositoryInterface
    )
    {}

    public function execute(PaymentTypeDTOs $paymentTypeDTOs)
    {
        $name = trim($paymentTypeDTOs->name);
        $note = trim($paymentTypeDTOs->note);

        /*if($note == '')
        {
            $note = 'لا يوجد ملاحظات.';
        }*/

        return $this->paymentTypeRepositoryInterface->create(
            new PaymentType(null , $name , $note)
        );
    }
}
