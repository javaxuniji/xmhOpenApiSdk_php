<?php

namespace XMH\OpenApiSdk\Order;

use XMH\OpenApiSdk\Client;

class Order
{
    const SyncOrderUrl = 'SyncPlatformOrder';
    private Client $client;
    public function __construct() {
        $this->client = new Client();
    }
    public function Sync(array $params): array {
        return $this->client->do(self::SyncOrderUrl, $params);
    }
}