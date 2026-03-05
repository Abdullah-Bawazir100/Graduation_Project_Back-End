<?php

namespace App\Application\User\DTOs;

class SignUpDTO {

    public function __construct(
        public string $firstName,
        public string $lastName,
        public int $departmentID
    ) {}

}