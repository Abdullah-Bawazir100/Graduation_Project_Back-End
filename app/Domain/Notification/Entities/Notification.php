<?php

namespace App\Domain\Notification\Entities;

use App\Domain\Notification\Enums\enNotificationType;
use App\Domain\User\Entities\User;

class Notification
{
    public function __construct(
        public ?int $id,
        public ?string $title,
        public ?string $description,
        public enNotificationType $notificationType,
        public ?string $receiverPhone,
        public ?User $sendBy
    )
    {}
}
