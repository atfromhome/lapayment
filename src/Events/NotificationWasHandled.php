<?php

declare(strict_types=1);

namespace FromHome\LaPayment\Events;

use FromHome\LaPayment\NotificationData;

final class NotificationWasHandled
{
    private NotificationData $data;

    public function __construct(NotificationData $data)
    {
        $this->data = $data;
    }

    public function getNotificationData(): NotificationData
    {
        return $this->data;
    }
}
