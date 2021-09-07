<?php

declare(strict_types=1);

namespace FromHome\LaPayment\Controller;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use FromHome\LaPayment\NotificationData;
use FromHome\LaPayment\MidtransSignature;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use FromHome\Payment\ValueObject\Transaction;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Resources\Json\JsonResource;
use FromHome\LaPayment\Events\NotificationWasFailed;
use FromHome\LaPayment\Events\NotificationWasHandled;
use FromHome\Payment\Input\CheckStatusTransactionInput;
use FromHome\Payment\Providers\Midtrans\MidtransClient;
use FromHome\LaPayment\Events\NotificationHasMismatchSignature;

final class MidtransNotificationController
{
    /**
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function handle(Request $request, Dispatcher $event, Repository $config, MidtransClient $client): JsonResource
    {
        $data = new NotificationData();
        $data->transactionId = $request->input('transaction_id');
        $data->orderId = $request->input('order_id');
        $data->status = $request->input('transaction_status');
        $data->amount = (float) $request->input('gross_amount');
        $data->originalData = (array) $request->input();

        try {
            if (MidtransSignature::verify($data->originalData, $request->input('signature_key'), $config->get('services.midtrans.secret'))) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $output = $client->status(
                    new CheckStatusTransactionInput(
                        new Transaction([
                            'id' => $data->transactionId,
                            'amount' => $data->amount,
                        ])
                    )
                );

                if ($output->getTransactionId() === $data->transactionId) {
                    $event->dispatch(new NotificationWasHandled($data));
                } else {
                    $event->dispatch(new NotificationWasFailed($data, 'Transaction not exists'));
                }
            } else {
                $event->dispatch(new NotificationHasMismatchSignature($data->originalData));
            }

            return new JsonResource([
                'status' => true,
                'message' => 'Notification was handled',
            ]);
        } catch (Exception $exception) {
            if (Container::getInstance()->bound(ExceptionHandler::class)) {
                /** @noinspection PhpUnhandledExceptionInspection */
                /** @var ExceptionHandler $handler */
                $handler = Container::getInstance()->make(ExceptionHandler::class);

                /** @noinspection PhpUnhandledExceptionInspection */
                $handler->report($exception);
            }

            $event->dispatch(new NotificationWasFailed($data, $exception->getMessage()));

            return new JsonResource([
                'status' => false,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
