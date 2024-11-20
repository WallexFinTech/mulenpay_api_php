<?php

namespace MulenPay\Validation;

use InvalidArgumentException;

class PaymentCreateValidator
{
    private const RUB_CURRENCY = 'rub';

    public function validate(array $data): void
    {
        // 0. Проверка обязательных полей
        $requiredFields = ['currency', 'amount', 'uuid', 'shopId', 'description', 'sign'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new InvalidArgumentException("Поле '$field' обязательно.");
            }
        }

        // 1. Проверка валюты
        if ($data['currency'] != self::RUB_CURRENCY) {
            throw new InvalidArgumentException("Поле 'currency' должно быть равно 'rub'.");
        }

        // 2. Проверка суммы
        if (!is_string($data['amount']) || !preg_match('/^\d+(\.\d{1,2})?$/', $data['amount'])) {
            throw new InvalidArgumentException("Поле 'amount' должно быть положительным числом в формате строки.");
        }

        // 3. Проверка UUID
        if (!is_string($data['uuid']) || !$this->isValidUUID($data['uuid'])) {
            throw new InvalidArgumentException("Поле 'uuid' должно быть строкой.");
        }

        // 4. Проверка ID магазина
        if (!is_int($data['shopId']) || $data['shopId'] <= 0) {
            throw new InvalidArgumentException("Поле 'shopId' должно быть положительным целым числом.");
        }

        // 5. Проверка описания
        if (!is_string($data['description']) || empty($data['description'])) {
            throw new InvalidArgumentException("Поле 'description' должно быть строкой.");
        }

        // 6. Проверка подписки (необязательное поле)
        if (isset($data['subscribe']) && !in_array($data['subscribe'], [null, 'Day', 'Week', 'Month'], true)) {
            throw new InvalidArgumentException("Поле 'subscribe' может быть null или одним из значений: 'Day', 'Week', 'Month'.");
        }

        // 7. Проверка времени холда
        if (isset($data['holdTime']) && (!is_int($data['holdTime']) || $data['holdTime'] <= 0)) {
            throw new InvalidArgumentException("Поле 'holdTime', если указано, должно быть положительным целым числом.");
        }

        // 8. Проверка подписи
        if (!is_string($data['sign']) || empty($data['sign'])) {
            throw new InvalidArgumentException("Поле 'sign' обязательно и должно быть строкой.");
        }
    }

    private function isValidUUID($uuid)
    {
        return preg_match('/^[a-f0-9]{8}-([a-f0-9]{4}-){3}[a-f0-9]{12}$/', $uuid);
    }
}