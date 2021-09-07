<?php

declare(strict_types=1);

namespace FromHome\LaPayment\Events;

use FromHome\LaPayment\NotificationData;

final class NotificationWasFailed
{
    private NotificationData $data;

    private string $message;

    public function __construct(NotificationData $data, string $message)
    {
        $this->data = $data;
        $this->message = $message;
    }

    public function getNotificationData(): NotificationData
    {
        return $this->data;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
