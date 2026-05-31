<?php

namespace App\Application\Notification\DTOs;

use App\Domain\Notification\Enums\enNotificationType;

class NotificationDTOs
{
    public function __construct(
        public ?string $title,
        public ?string $description,
        public enNotificationType $notificationType,
        public ?string $receiverPhone,
        public ?int $sendBy
    )
    {}
}
