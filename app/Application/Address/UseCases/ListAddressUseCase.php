<?php

namespace App\Application\Address\UseCases;

use App\Domain\Address\Repositories\AddressRepositoryInterface;

class ListAddressUseCase
{
    public function __construct(
        private AddressRepositoryInterface $address_repository_interface
    )
    {}

    public function execute()
    {
        return $this->address_repository_interface->getAll();
    }
}