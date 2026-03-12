<?php

namespace App\Domain\User\Enums;

enum UserRole: string
{
    case Admin = 'Admin';
    case Manager = 'Manager';
    case Employee = 'Employee';
    case Tax_payer = 'Tax_payer';
}
