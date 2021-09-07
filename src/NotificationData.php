<?php

declare(strict_types=1);

namespace FromHome\LaPayment;

final class NotificationData
{
    public string $transactionId;

    public string $status;

    public string $orderId;

    public float $amount;

    public array $originalData;
}
