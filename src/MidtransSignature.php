<?php

declare(strict_types=1);

namespace FromHome\LaPayment;

final class MidtransSignature
{
    public static function verify(array $data, string $hashingSignature, string $serverKey): bool
    {
        $stringToHash = \sprintf('%s%s%s%s', $data['order_id'] ?? '', $data['status_code'] ?? '', $data['gross_amount'] ?? '', $serverKey);

        $stringHash = \hash('sha512', $stringToHash);

        return \hash_equals($stringHash, $hashingSignature);
    }
}
