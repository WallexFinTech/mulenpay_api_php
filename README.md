# MulenPay PHP SDK

PHP SDK для интеграции с MulenPay API.

## Установка

```bash
composer require sq-dev/mulenpay
```

# Требования

* PHP 8.1 или выше

# Установленные зависимости:

* guzzlehttp/guzzle:^7.9 (для HTTP-запросов)
* ext-json:* (для работы с JSON)

Использование

Настройка клиента

Для начала работы создайте экземпляры классов Payment или Subscribe, указав ваш api_key и secret_key.

```php
<?php

require 'vendor/autoload.php';

use MulenPay\Payment;
use MulenPay\Subscribe;

$apiKey = 'ВАШ_API_KEY';
$secretKey = 'ВАШ_SECRET_KEY';

$payment = new Payment($apiKey, $secretKey);
$subscribe = new Subscribe($apiKey);
```

## Работа с платежами

Создание платежа
```php
<?php
$response = $payment->createPayment([
    'currency' => 'rub',
    'amount' => '1000.50',
    'uuid' => 'invoice_123',
    'shopId' => 5,
    'description' => 'Покупка булочек',
    'subscribe' => null,
    'holdTime' => null
]);

echo $response;
```

Получение списка платежей
```php
<?php

$response = $payment->getPaymentList();

echo $response;
```

Получение платежа по ID
```php
<?php

$response = $payment->getPaymentById(5);

echo $response;
```

Подтверждение платежа
```php
<?php

$response = $payment->confirmPayment(5);

echo $response;
```

Отмена платежа
```php
<?php

$response = $payment->cancelPayment(5);

echo $response;
```

Возврат платежа
```php
<?php

$response = $payment->refundPayment(5);

echo $response;
```

## Работа с подписками
Получение списка подписок
```php
<?php

$response = $subscribe->getSubscriptionList();

echo $response;
```

Удаление подписки по ID
```php
<?php

$response = $subscribe->deleteSubscriptionById(5);

echo $response;
```