<?php

namespace XMH\OpenApiSdk\Item;

use XMH\OpenApiSdk\Client;
class Item {
    const SyncProductUrl = 'SyncPlatformProduct';
    private Client $client;
    public function __construct() {
        $this->client = new Client();
    }
    public function Sync(array $params): array {
        return $this->client->do(self::SyncProductUrl, $params);
    }

}