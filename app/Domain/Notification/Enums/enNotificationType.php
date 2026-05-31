<?php

namespace App\Domain\Notification\Enums;

enum enNotificationType: string
{
    case General = 'General';
    case ForSystemUsers = 'ForSystemUsers';
    case ForTaxPayers = 'ForTaxPayers';
    case Special = 'Special';

    public function label(): string
    {
        return match($this) {
            self::General => 'عامة',
            self::ForSystemUsers => 'مستخدمي النظام',
            self::ForTaxPayers => 'مكلفين',
            self::Special => 'خاصة',
        };
    }
}
