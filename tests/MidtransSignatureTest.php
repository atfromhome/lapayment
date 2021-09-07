<?php

declare(strict_types=1);

namespace FromHome\LaPayment\Tests;

use PHPUnit\Framework\TestCase;
use FromHome\LaPayment\MidtransSignature;

final class MidtransSignatureTest extends TestCase
{
    public function testCanVerifySignature(): void
    {
        $stringToHash = '12001000server-key';

        $hash = \hash('sha512', $stringToHash);

        $this->assertTrue(MidtransSignature::verify([
            'order_id' => '1',
            'status_code' => 200,
            'gross_amount' => 1000,
        ], $hash, 'server-key'));
    }
}
