# LaPayment

Ini adalah integrasi Laravel dengan `fromhome/payment` package, provide basik notification handling.

## Install

Jalankan perintah ini untuk menginstall

```bash
composer require fromhome/lapayment
```

> Pastikan menggunakan PHP 8.0 dan Laravel ^8.58 untuk menggunakan package ini.

## Register route

Untuk menggunakan notification handling terlebih dahulu kita buat route

```php
Route::post('/url/hire' [FromHome\LaPayment\Controller\MidtransNotificationController::class, 'handle']);
```

## Services Config

Sebelum menggunakan pastikan anda menambahkan config di `config/services.php`

```php
'midtrans' => [
    'key' => env('MIDTRANS_SERVER_KEY'),
    'secret' => env('MIDTRANS_SERVER_SECRET'),
    'notification' => [
        'append' => 'https://example.com', // Isi dengan notification route
        'override' => 'https://example.com', // Isi dengan notification route
    ]
]
```

Untuk mendapatkan `key` dan `secret` bisa
baca [disini](https://docs.midtrans.com/en/midtrans-account/overview?id=retrieving-api-access-keys)
`Client Key` sebagai `key` dan `Server Key` untuk `secret`
