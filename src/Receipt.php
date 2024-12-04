<?php

namespace MulenPay;

class Receipt extends AMulenPayClient
{
    const URI = '/api/v2/payments/';

    public function getReceiptByID(int $receiptId): array
    {
        return $this->request(self::GET, self::URI . $receiptId . '/receipt');
    }
}