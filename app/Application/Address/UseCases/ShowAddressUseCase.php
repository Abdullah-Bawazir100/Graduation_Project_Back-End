<?php

namespace App\Application\Address\UseCases;

use App\Domain\Address\Repositories\AddressRepositoryInterface;

class ShowAddressUseCase
{
    public function __construct(
        private AddressRepositoryInterface $address_repository_interface
    )
    {}

    public function execute(int $id)
    {
        return $this->address_repository_interface->findById($id);
    }
}