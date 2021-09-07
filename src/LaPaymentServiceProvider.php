<?php

declare(strict_types=1);

namespace FromHome\LaPayment;

use FromHome\Payment\Credentials;
use FromHome\Payment\PaymentFactory;
use Illuminate\Support\ServiceProvider;
use FromHome\Payment\Providers\Midtrans\MidtransClient;
use FromHome\Payment\Providers\Midtrans\SnapMidtransClient;

final class LaPaymentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerMidtrans();
    }

    protected function registerMidtrans(): void
    {
        $midtransCredentials = new Credentials(
            \config('services.midtrans.key'),
            \config('services.midtrans.secret'),
        );

        $configurations = [
            'isProduction' => $this->app->environment('production'),
            'appendNotification' => \config('services.midtrans.notification.append'),
            'overrideNotification' => \config('services.midtrans.notification.override'),
        ];

        $this->app->singleton(MidtransClient::class, fn () => PaymentFactory::createMidtransClient(
            $midtransCredentials,
            $configurations
        ));

        $this->app->singleton(SnapMidtransClient::class, fn () => PaymentFactory::createSnapMidtransClient(
            $midtransCredentials,
            $configurations
        ));
    }
}
