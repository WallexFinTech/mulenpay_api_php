<?php

namespace MulenPay;

class Subscribe extends AMulenPayClient
{
    const URI = '/api/v2/subscribes';

    public function getSubscriptionList(int $page = 1): array
    {
        return $this->request(self::GET, self::URI."?page={$page}");
    }

    public function cancelSubscription(int $subscribe_id): array
    {
        return $this->request(self::DELETE, self::URI."/".$subscribe_id);
    }
}