<?php

namespace MulenPay;

use MulenPay\Validation\PaymentCreateValidator;

class Payment extends AMulenPayClient
{
    private string $secretKey;
    private const PAYMENTS = '/api/v2/payments';

    public function __construct(string $apiKey, $secretKey)
    {
        parent::__construct($apiKey);
        $this->secretKey = $secretKey;
    }

    public function createPayment(array $data): array
    {
        $data = $this->prepareData($data);

        $validator = new PaymentCreateValidator();
        $validator->validate($data);

        return $this->request(self::POST, self::PAYMENTS, $data);
    }

    public function getPaymentList(int $page = 1): array
    {
        return $this->request(self::GET, self::PAYMENTS."?page={$page}");
    }

    public function getPaymentById(int $paymentId): array
    {
        return $this->request(self::GET, self::PAYMENTS."/".$paymentId);
    }

    public function confirmPayment(int $paymentId): array
    {
        return $this->request(self::PUT, self::PAYMENTS."/{$paymentId}/hold");
    }

    public function cancelPayment(int $paymentId): array
    {
        return $this->request(self::DELETE, self::PAYMENTS."/{$paymentId}/hold");
    }

    public function refundPayment(int $paymentId): array
    {
        return $this->request(self::PUT, self::PAYMENTS."/{$paymentId}/refund");
    }

    private function prepareData(array $data): array
    {
        $data['sign'] = $this->calculateSign($data);
        return $data;
    }

    private function calculateSign(array $data): string
    {
        $dataToSign = array_intersect_key($data, array_flip(['currency', 'amount', 'shopId']));
        ksort($dataToSign);
        $signString = implode('|', $dataToSign) . '|' . $this->secretKey;
        return hash('sha256', $signString);
    }

}