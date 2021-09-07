<?php

declare(strict_types=1);

namespace FromHome\LaPayment\Tests\Controller;

use Illuminate\Support\Facades\Event;
use FromHome\LaPayment\Tests\TestCase;
use FromHome\Payment\Providers\Midtrans\Client;
use Symfony\Component\HttpClient\MockHttpClient;
use FromHome\LaPayment\Events\NotificationWasFailed;
use FromHome\LaPayment\Events\NotificationWasHandled;
use Symfony\Component\HttpClient\Response\MockResponse;
use FromHome\LaPayment\Events\NotificationHasMismatchSignature;

class MidtransNotificationControllerTest extends TestCase
{
    public function testCanProcessNotification(): void
    {
        $json = <<<JSON
{  "transaction_time": "2021-06-21 23:31:51",  "transaction_status": "settlement",  "transaction_id": "f8635cd7-615d-4a6d-a806-c9ca4a56257e",  "status_message": "midtrans payment notification",  "status_code": "200",  "signature_key": "44584aba0621a349c7396a6510c9a4629c03aa15a17cd124705972dc2ab0b228edb138a1d893ab5bd72e8cda952dc5000e622eb6bba033d0e3b89289475d399c",  "settlement_time": "2021-06-21 23:32:40",  "payment_type": "bri_epay",  "order_id": "bri-epay-01",  "merchant_id": "G141532850",  "gross_amount": "5622200.00",  "fraud_status": "accept",  "currency": "IDR",  "approval_code": "ABC0101BCA02"}
JSON;

        $this->registerMockMidtrans(new MockHttpClient([
            new MockResponse($json),
        ], Client::SANDBOX_URL));

        Event::fake([NotificationWasHandled::class, NotificationHasMismatchSignature::class]);

        $this->post('/midtrans/callback', \json_decode(
            $json,
            true
        ))->assertSuccessful();

        Event::assertDispatched(NotificationWasHandled::class, function (NotificationWasHandled $event) {
            $data = $event->getNotificationData();
            return $data->transactionId === 'f8635cd7-615d-4a6d-a806-c9ca4a56257e'
                && $data->orderId === 'bri-epay-01';
        });
        Event::assertNotDispatched(NotificationWasFailed::class);
        Event::assertNotDispatched(NotificationHasMismatchSignature::class);
    }

    public function testCannotProcessNotification(): void
    {
        $json = <<<JSON
{  "transaction_time": "2021-06-21 23:31:51",  "transaction_status": "settlement",  "transaction_id": "f8635cd7-615d-4a6d-a806-c9ca4a56257e",  "status_message": "midtrans payment notification",  "status_code": "200",  "signature_key": "34584aba0621a349c7396a6510c9a4629c03aa15a17cd124705972dc2ab0b228edb138a1d893ab5bd72e8cda952dc5000e622eb6bba033d0e3b89289475d399c",  "settlement_time": "2021-06-21 23:32:40",  "payment_type": "bri_epay",  "order_id": "bri-epay-01",  "merchant_id": "G141532850",  "gross_amount": "5622200.00",  "fraud_status": "accept",  "currency": "IDR",  "approval_code": "ABC0101BCA02"}
JSON;

        $this->registerMockMidtrans(new MockHttpClient([
            new MockResponse($json),
        ], Client::SANDBOX_URL));
        Event::fake([NotificationWasHandled::class, NotificationHasMismatchSignature::class]);

        $this->post('/midtrans/callback', \json_decode(
            $json,
            true
        ))->assertSuccessful();

        Event::assertNotDispatched(NotificationWasFailed::class);
        Event::assertNotDispatched(NotificationWasHandled::class);
        Event::assertDispatched(NotificationHasMismatchSignature::class);
    }
}
