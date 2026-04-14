<?php

namespace App\Domain\PaymentType\Repositories;

use App\Domain\PaymentType\Entities\PaymentType;

interface PaymentTypeRepositoryInterface
{
    public function create(PaymentType $paymentType);
    public function update(PaymentType $paymentType);
    public function delete(int $id);
    public function findById(int $id);
    public function getAll();
    public function existsByName(string $name);
}
