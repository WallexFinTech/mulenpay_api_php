<?php

namespace MulenPay\Validation;

use InvalidArgumentException;

/**
 * Class PaymentCreateValidator
 *
 * Validates the JSON request body for payment creation.
 *
 * Example of a valid JSON request:
 * {
 *    "currency": "rub",                   // The currency code (required, must be "rub").
 *    "amount": "1000.50",                 // The payment amount (required, numeric).
 *    "uuid": "invoice_123",               // Unique identifier for the invoice (required, string).
 *    "shopId": 5,                         // Shop ID (required, integer).
 *    "description": "Покупка булочек",    // Payment description (required, string).
 *    "subscribe": null,                   // Subscription type (optional, null or enum: ["Day", "Week", "Month"]).
 *    "holdTime": null,                    // Hold time for the payment (optional).
 *    "items": [                           // Array of items included in the payment (optional).
 *      {
 *        "description": "string",         // Item description (required, string).
 *        "quantity": 0,                   // Quantity of the item (required, numeric).
 *        "price": 0,                      // Price per item (required, numeric).
 *        "vat_code": 0,                   // VAT code (required, enum).
 *        "payment_subject": 1,            // Payment subject (required, enum).
 *        "payment_mode": 1,               // Payment mode (required, enum).
 *        "product_code": "string",        // Product code (optional, string or null).
 *        "country_of_origin_code": "string", // Country of origin code (optional, string or null).
 *        "customs_declaration_number": "string", // Customs declaration number (optional, string or null).
 *        "excise": "string",              // Excise value (optional, string or null).
 *        "measurement_unit": 0            // Measurement unit (optional, enum).
 *      }
 *    ],
 *    "sign": "string"                     // Digital signature of the request (required, string).
 * }
 */
class PaymentCreateValidator
{
    const RUB_CURRENCY = 'rub';
    const REQUIRED_FIELDS = ['currency', 'amount', 'uuid', 'shopId', 'description', 'sign'];
    const REQUIRED_ITEM_FIELDS = ['description', 'quantity', 'price', 'vat_code', 'payment_subject', 'payment_mode'];
    const SUBSCRIBE = [null, 'Day', 'Week', 'Month'];
    const ITEM_VAT_CODES = [
        0 => 'Без НДС',
        1 => 'НДС по ставке 0%',
        2 => 'НДС чека по ставке 10%',
        3 => 'НДС чека по ставке 18%',
        4 => 'НДС чека по расчетной ставке 10/110',
        5 => 'НДС чека по расчетной ставке 18/118',
        6 => 'НДС чека по ставке 20%',
        7 => 'НДС чека по расчетной ставке 20/120',
    ];
    const ITEM_PAYMENT_SUBJECTS = [
        1 => 'Товар',
        2 => 'Подакцизный товар',
        3 => 'Работа',
        4 => 'Услуга',
        5 => 'Ставка азартной игры',
        6 => 'Выигрыш азартной игры',
        7 => 'Лотерейный билет',
        8 => 'Выигрыш лотереи',
        9 => 'Предоставление РИД',
        10 => 'Платеж',
        11 => 'Агентское вознаграждение',
        12 => 'Выплата',
        13 => 'Иной предмет расчета',
        14 => 'Имущественное право',
        15 => 'Внереализационный доход',
        16 => 'Иные платежи и взносы',
        17 => 'Торговый сбор',
        19 => 'Залог',
        20 => 'Расход',
        21 => 'Взносы на обязательное пенсионное страхование ИП',
        22 => 'Взносы на обязательное пенсионное страхование',
        23 => 'Взносы на обязательное медицинское страхование ИП',
        24 => 'Взносы на обязательное медицинское страхование',
        25 => 'Взносы на обязательное социальное страхование',
        26 => 'Платеж казино',
    ];

    const ITEM_PAYMENT_MODES = [
        1 => 'Полная предоплата',
        2 => 'Частичная предоплата',
        3 => 'Аванс',
        4 => 'Полный расчет',
        5 => 'Частичный расчет и кредит',
        6 => 'Кредит',
        7 => 'Выплата по кредиту',
    ];

    const ITEM_MEASUREMENT_UNITS = [
        0 => 'Штука или единица (шт. или ед.)',
        10 => 'Грамм (г)',
        11 => 'Килограмм (кг)',
        12 => 'Тонна (т)',
        20 => 'Сантиметр (см)',
        21 => 'Дециметр (дм)',
        22 => 'Метр (м)',
        30 => 'Квадратный сантиметр (кв. см)',
        31 => 'Квадратный дециметр (кв. дм)',
        32 => 'Квадратный метр (кв. м)',
        40 => 'Миллилитр (мл)',
        41 => 'Литр (л)',
        42 => 'Кубический метр (куб. м)',
        50 => 'Киловатт час (кВт · ч)',
        51 => 'Гигакалория (Гкал)',
        70 => 'Сутки (сутки)',
        71 => 'Час (час)',
        72 => 'Минута (мин)',
        73 => 'Секунда (с)',
        80 => 'Килобайт (Кбайт)',
        81 => 'Мегабайт (Мбайт)',
        82 => 'Гигабайт (Гбайт)',
        83 => 'Терабайт (Тбайт)',
        255 => 'Применяется при использовании иных единиц измерения, не поименованных в п. п. 1 - 23',
    ];

    public function validate(array $data): void
    {
        $this->validateFieldsExist($data, self::REQUIRED_FIELDS);
        if ($data['currency'] !== self::RUB_CURRENCY) {
            throw new InvalidArgumentException("Поле 'currency' должно быть " . self::RUB_CURRENCY);
        }

        $this->validateNumericField($data['amount'], 'amount');
        $this->validateStringField($data['uuid'], 'uuid');
        $this->validateIntField($data['shopId'], 'shopId');
        $this->validateStringField($data['description'], 'description');

        if (isset($data['subscribe']) && !in_array($data['subscribe'], self::SUBSCRIBE, true)) {
            throw new InvalidArgumentException("Поле 'subscribe' может быть null или одним из значений: 'Day', 'Week', 'Month'.");
        }
        if (isset($data['holdTime']) && !is_int($data['holdTime'])) {
            throw new InvalidArgumentException("Поле 'holdTime', если указано, должно быть целым числом.");
        }
        $this->validateItems($data['items'] ?? []);
        $this->validateStringField($data['sign'], 'sign');
    }

    private function validateFieldsExist(array $data, array $fields): void
    {
        foreach ($fields as $field) {
            if (!isset($data[$field])) {
                throw new InvalidArgumentException("Поле '$field' обязательно.");
            }
        }
    }

    private function validateNumericField($value, string $fieldName): void
    {
        if (!is_numeric($value)) throw new InvalidArgumentException("Поле '$fieldName' должно быть числом.");
    }

    private function validateStringField($value, string $fieldName): void
    {
        if (!is_string($value)) throw new InvalidArgumentException("Поле '$fieldName' должно быть строкой.");
    }

    private function validateIntField($value, string $fieldName): void
    {
        if (!is_int($value)) throw new InvalidArgumentException("Поле '$fieldName' должно быть целым числом.");
    }

    private function validateItems(array $items): void
    {
        foreach ($items as $index => $item) {
            $this->validateFieldsExist($item, self::REQUIRED_ITEM_FIELDS);
            $this->validateStringField($item['description'], "items[$index].description");
            $this->validateNumericField($item['quantity'], "items[$index].quantity");
            $this->validateNumericField($item['price'], "items[$index].price");
            $this->validateEnumKey($item['vat_code'], self::ITEM_VAT_CODES, "items[$index].vat_code");
            $this->validateEnumKey($item['payment_subject'], self::ITEM_PAYMENT_SUBJECTS, "items[$index].payment_subject");
            $this->validateEnumKey($item['payment_mode'], self::ITEM_PAYMENT_MODES, "items[$index].payment_mode");

            $this->validateStringOrNull($item['product_code'] ?? null, "items[$index].product_code");
            $this->validateStringOrNull($item['country_of_origin_code'] ?? null, "items[$index].country_of_origin_code");
            $this->validateStringOrNull($item['customs_declaration_number'] ?? null, "items[$index].customs_declaration_number");
            $this->validateStringOrNull($item['excise'] ?? null, "items[$index].excise");

            $this->validateMeasurementUnit($item['measurement_unit'] ?? null, $index);
        }
    }

    private function validateEnumKey($value, array $enum, string $fieldName): void
    {
        if (!array_key_exists($value, $enum)) {
            $description = $this->formatEnumValues($enum);
            throw new InvalidArgumentException("Поле '$fieldName' должно быть одним из следующих значений:\n $description");
        }
    }

    private function validateMeasurementUnit(?int $measurementUnit, int $index): void
    {
        if ($measurementUnit !== null && !array_key_exists($measurementUnit, self::ITEM_MEASUREMENT_UNITS)) {
            $description = $this->formatEnumValues(self::ITEM_MEASUREMENT_UNITS);
            throw new InvalidArgumentException("Поле 'measurement_unit' в элементе массива 'items' (индекс $index) должно быть одним из следующих значений:\n$description");
        }
    }

    private function formatEnumValues(array $enumValues): string
    {
        return implode("\n",
            array_map(fn($key, $value) => "$key: $value",
                array_keys($enumValues),
                $enumValues
            ));
    }

    private function validateStringOrNull(?string $value, string $fieldName): void
    {
        if ($value !== null && !is_string($value)) {
            throw new InvalidArgumentException("Поле '$fieldName' должно быть строкой или null.");
        }
    }
}