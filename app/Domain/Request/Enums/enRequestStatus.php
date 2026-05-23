<?php

namespace App\Domain\Request\Enums;

use Illuminate\Validation\Rules\Enum;

enum EnRequestStatus: string
{
    case Pending = 'Pending';
    case Confirmed = 'Confirmed';
    case Archived = 'Archived';
    case Rejected = 'Rejected';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'قيد الإنتظار',
            self::Confirmed => 'تم التأكيد',
            self::Archived => 'تم الترحيل',
            self::Rejected => 'تم الرفض',
        };
    }
}
