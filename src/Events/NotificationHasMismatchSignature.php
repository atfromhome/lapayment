<?php

declare(strict_types=1);

namespace FromHome\LaPayment\Events;

final class NotificationHasMismatchSignature
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getNotificationData(): array
    {
        return $this->data;
    }
}
