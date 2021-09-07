<?php

declare(strict_types=1);

namespace FromHome\LaPayment\Tests;

use FromHome\Payment\Credentials;
use FromHome\Payment\PaymentFactory;
use FromHome\LaPayment\LaPaymentServiceProvider;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use FromHome\Payment\Providers\Midtrans\MidtransClient;
use FromHome\Payment\Providers\Midtrans\SnapMidtransClient;
use FromHome\LaPayment\Controller\MidtransNotificationController;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaPaymentServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('services.midtrans', [
            'key' => 'some-key',
            'secret' => 'some-secret-server',
            'notification' => [
                'append' => 'https://example.com',
                'override' => 'https://example.com',
            ],
        ]);
    }

    protected function defineRoutes($router): void
    {
        $router->post('/midtrans/callback', [MidtransNotificationController::class, 'handle']);
    }

    protected function registerMockMidtrans(HttpClientInterface $httpClient): void
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
            $configurations,
            null,
            null,
            $httpClient
        ));

        $this->app->singleton(SnapMidtransClient::class, fn () => PaymentFactory::createSnapMidtransClient(
            $midtransCredentials,
            $configurations,
            null,
            null,
            $httpClient
        ));
    }
}
