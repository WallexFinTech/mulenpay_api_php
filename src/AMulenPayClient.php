<?php

namespace MulenPay;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

abstract class AMulenPayClient
{
    protected Client $client;
    protected string $baseUrl = 'https://mulenpay.ru';
    protected string $apiKey;

    const POST = 'POST';
    const GET = 'GET';
    const PUT = 'PUT';
    const DELETE = 'DELETE';

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => "Bearer $apiKey",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    protected function request(string $method, string $uri, array $data = [])
    {
        try {
            $response = $this->client->request($method, $uri, ['json' => $data]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $errorResponse = $e->hasResponse()
                ? json_decode($e->getResponse()->getBody()->getContents(), true)
                : ['error' => $e->getMessage()];
            throw new \RuntimeException($errorResponse['message'] ?? $e->getMessage(), $e->getCode());
        }
    }
}